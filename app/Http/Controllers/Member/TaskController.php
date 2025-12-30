<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\TaskLinkHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // TAMBAHKAN INI

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Perbaikan: Cek role dengan benar
            $user = Auth::user();
            if (!$user || $user->role !== 'member') {
                abort(403, 'Unauthorized access. This area is for members only.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the member's tasks.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Query untuk tasks yang di-assign ke user ini
        $query = Task::with(['project', 'assignee'])
            ->where('assigned_to', $userId);

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter berdasarkan project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter tasks with links only
        if ($request->has('with_links') && $request->with_links == '1') {
            $query->whereNotNull('link');
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('link', 'like', "%{$search}%");
            });
        }

        // Filter overdue
        if ($request->has('overdue') && $request->overdue == '1') {
            $query->where('deadline', '<', now())->where('status', '!=', 'done');
        }

        // Filter due today
        if ($request->has('due_today') && $request->due_today == '1') {
            $query->whereDate('deadline', today())->where('status', '!=', 'done');
        }

        // Sort
        $sort = $request->get('sort', 'deadline');
        $direction = $request->get('direction', 'asc');

        $allowedSort = ['title', 'status', 'priority', 'deadline', 'progress', 'created_at'];
        if (in_array($sort, $allowedSort)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('deadline', 'asc');
        }

        $tasks = $query->paginate(15)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total' => Task::where('assigned_to', $userId)->count(),
            'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
            'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
            'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
            'overdue' => Task::where('assigned_to', $userId)
                ->where('deadline', '<', now())
                ->where('status', '!=', 'done')
                ->count(),
            'due_today' => Task::where('assigned_to', $userId)
                ->whereDate('deadline', today())
                ->where('status', '!=', 'done')
                ->count(),
            'with_link' => Task::where('assigned_to', $userId)->whereNotNull('link')->count(),
        ];

        // Projects yang user ikuti (asumsi ada relationship 'members')
        $projects = Project::where(function($q) use ($userId) {
            // Jika ada relationship members
            if (method_exists(Project::class, 'members')) {
                $q->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            }
            // Atau jika user adalah project manager
            $q->orWhere('project_manager_id', $userId);
        })->get();

        // Get today's tasks for calendar
        $todayTasks = Task::where('assigned_to', $userId)
            ->whereDate('deadline', today())
            ->where('status', '!=', 'done')
            ->count();

        return view('member.tasks.index', compact('tasks', 'stats', 'projects', 'todayTasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $userId = Auth::id();
        
        // Get projects accessible by the member
        $projects = Project::where(function($q) use ($userId) {
            if (method_exists(Project::class, 'members')) {
                $q->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            }
            $q->orWhere('project_manager_id', $userId);
        })->get();

        return view('member.tasks.create', compact('projects'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
        ]);

        // Member hanya bisa membuat task untuk dirinya sendiri
        $validated['assigned_to'] = Auth::id();
        $validated['status'] = 'todo';
        $validated['progress'] = 0;

        Task::create($validated);

        return redirect()->route('member.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to view this task.');
        }

        $task->load(['project', 'assignee', 'comments.user', 'linkHistories.user']);

        // Get similar tasks
        $similarTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->where('assigned_to', $userId)
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        // Get link history
        $linkHistory = $task->linkHistories()->with('user')->latest()->get();

        return view('member.tasks.show', compact('task', 'similarTasks', 'linkHistory'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to edit this task.');
        }

        // Get projects accessible by the member
        $projects = Project::where(function($q) use ($userId) {
            if (method_exists(Project::class, 'members')) {
                $q->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            }
            $q->orWhere('project_manager_id', $userId);
        })->get();

        return view('member.tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        // Debug log
        Log::info('Task Update called', [ // DIPERBAIKI: Gunakan Log::
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'task_assigned_to' => $task->assigned_to,
            'request_method' => $request->method(),
            'request_data' => $request->except(['_token', '_method']),
        ]);
        
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to update this task.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
            'status' => 'required|in:todo,in_progress,done',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        Log::info('Task Update validated', ['validated_data' => $validated]); // DIPERBAIKI: Gunakan Log::

        $task->update($validated);

        Log::info('Task Update successful', ['task_updated' => $task->id]); // DIPERBAIKI: Gunakan Log::

        return redirect()->route('member.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to delete this task.');
        }

        $task->delete();

        return redirect()->route('member.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Update the specified task status/progress.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to update this task.');
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $oldStatus = $task->status;
        $task->status = $validated['status'];

        // If marking as done, set progress to 100%
        if ($validated['status'] === 'done') {
            $task->progress = 100;
            $task->completed_at = now();
        } elseif ($validated['status'] === 'in_progress' && $task->progress == 0) {
            // If starting work, set progress to 10%
            $task->progress = 10;
        }

        $task->save();

        // Create status change comment
        $commentMessage = "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($validated['status']);
        $this->addCommentToTask($task, $commentMessage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => $task->fresh(),
                'new_status' => $task->status,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task status updated successfully.');
    }

    /**
     * Update task progress via AJAX.
     */
    public function updateProgress(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $oldProgress = $task->progress;
        $oldStatus = $task->status;
        
        $task->progress = $request->progress;

        // Auto-update status based on progress
        if ($request->progress >= 100) {
            $task->status = 'done';
            $task->completed_at = now();
        } elseif ($request->progress > 0 && $task->status === 'todo') {
            $task->status = 'in_progress';
        } elseif ($request->progress == 0) {
            $task->status = 'todo';
        }

        $task->save();

        // If status changed, add comment
        if ($oldStatus !== $task->status) {
            $commentMessage = "Progress: {$oldProgress}% → {$request->progress}%\nStatus: " . 
                            ucfirst($oldStatus) . " → " . ucfirst($task->status);
            $this->addCommentToTask($task, $commentMessage);
        }

        // Update project progress (asumsi ada method updateProgress di model Project)
        if ($task->project && method_exists($task->project, 'updateProgress')) {
            $task->project->updateProgress();
        }

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'old_progress' => $oldProgress,
            'new_progress' => $task->progress,
            'status' => $task->status,
        ]);
    }

    /**
     * Get tasks calendar data for the member.
     */
    public function calendar(Request $request)
    {
        $userId = Auth::id();

        $start = $request->get('start', date('Y-m-01'));
        $end = $request->get('end', date('Y-m-t'));

        $tasks = Task::where('assigned_to', $userId)
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [$start, $end])
            ->with('project')
            ->get();

        $events = $tasks->map(function ($task) {
            $eventColor = match($task->priority) {
                'high' => '#dc3545',
                'medium' => '#ffc107',
                'low' => '#28a745',
                default => '#6c757d',
            };

            // Adjust color based on status
            $isOverdue = $task->deadline && $task->deadline < now() && $task->status !== 'done';
            if ($isOverdue) {
                $eventColor = '#dc3545';
            } elseif ($task->status === 'done') {
                $eventColor = '#28a745';
            }

            return [
                'id' => $task->id,
                'title' => $task->title . ($task->progress ? " ({$task->progress}%)" : ''),
                'start' => $task->deadline->format('Y-m-d'),
                'end' => $task->deadline->format('Y-m-d'),
                'color' => $eventColor,
                'textColor' => '#ffffff',
                'url' => route('member.tasks.show', $task),
                'extendedProps' => [
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'progress' => $task->progress,
                    'project' => $task->project->name ?? 'No Project',
                    'has_link' => !empty($task->link),
                    'description' => substr($task->description ?? '', 0, 100),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Validate link for security.
     */
    private function validateLink($link)
    {
        // Cek apakah link tidak kosong
        if (empty($link)) {
            return false;
        }
        
        // Cek apakah link valid menggunakan filter_var
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Domain yang diizinkan
        $allowedDomains = [
            'drive.google.com',
            'docs.google.com',
            'github.com',
            'gitlab.com',
            'figma.com',
            'trello.com',
            'asana.com',
            'notion.so',
            'miro.com',
            'slack.com',
            'dropbox.com',
            'onedrive.live.com',
            'sharepoint.com',
            'airtable.com',
            'youtube.com',
            'vimeo.com',
            'loom.com',
        ];
        
        $parsedUrl = parse_url($link);
        
        // Pastikan parse_url berhasil dan memiliki host
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return false;
        }
        
        $domain = $parsedUrl['host'];
        
        // Remove www. if present
        if (strpos($domain, 'www.') === 0) {
            $domain = substr($domain, 4);
        }
        
        // Cek apakah domain ada di daftar yang diizinkan
        return in_array($domain, $allowedDomains);
    }

    /**
     * Submit link for task (for members).
     */
    public function submitLink(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to submit link for this task.');
        }

        $validator = Validator::make($request->all(), [
            'link' => 'required|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate link security
        if (!$this->validateLink($request->link)) {
            return response()->json([
                'success' => false,
                'message' => 'Link is not from an allowed domain or is invalid.'
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $validated = $validator->validated();

        // Simpan data lama untuk history
        $oldStatus = $task->status;
        $oldLink = $task->link;
        $oldProgress = $task->progress;

        // Update task
        $task->link = $validated['link'];
        
        // Update status jika masih todo
        if ($task->status === 'todo') {
            $task->status = 'in_progress';
        }
        
        // Tambah progress otomatis jika kurang dari 100
        if ($task->progress < 100) {
            $task->progress = min(100, $task->progress + 25);
        }
        
        $task->save();

        // Buat history link
        TaskLinkHistory::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'old_link' => $oldLink,
            'new_link' => $validated['link'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Buat komentar otomatis
        $commentMessage = "**Link Submitted**\n";
        $commentMessage .= "🔗 New Link: " . $validated['link'] . "\n";
        
        if ($oldLink) {
            $commentMessage .= "📎 Previous Link: " . $oldLink . "\n";
        }
        
        if (!empty($validated['notes'])) {
            $commentMessage .= "\n📝 Notes: " . $validated['notes'];
        }
        
        $commentMessage .= "\n\n📊 Progress: {$oldProgress}% → {$task->progress}%";
        
        if ($oldStatus !== $task->status) {
            $commentMessage .= "\n📋 Status: " . ucfirst($oldStatus) . " → " . ucfirst($task->status);
        }

        $this->addCommentToTask($task, $commentMessage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Link submitted successfully',
                'task' => $task->fresh(),
                'new_link' => $task->link,
                'new_status' => $task->status,
                'new_progress' => $task->progress,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Link submitted successfully!');
    }

    /**
     * Update task link (for members).
     */
    public function updateLink(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to update link for this task.');
        }

        $validator = Validator::make($request->all(), [
            'link' => 'required|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate link security
        if (!$this->validateLink($request->link)) {
            return response()->json([
                'success' => false,
                'message' => 'Link is not from an allowed domain or is invalid.'
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $validated = $validator->validated();
        $oldLink = $task->link;

        // Update task link
        $task->link = $validated['link'];
        $task->save();

        // Buat history link
        TaskLinkHistory::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'old_link' => $oldLink,
            'new_link' => $validated['link'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Buat komentar otomatis
        $commentMessage = "**Link Updated**\n";
        $commentMessage .= "🔗 New Link: " . $validated['link'] . "\n";
        $commentMessage .= "📎 Previous Link: " . ($oldLink ?: 'No link');
        
        if (!empty($validated['notes'])) {
            $commentMessage .= "\n\n📝 Notes: " . $validated['notes'];
        }

        $this->addCommentToTask($task, $commentMessage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Link updated successfully',
                'task' => $task->fresh(),
                'new_link' => $task->link,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Link updated successfully!');
    }

    /**
     * Remove link from task.
     */
    public function removeLink(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to remove link from this task.');
        }

        $oldLink = $task->link;
        
        // Buat history sebelum menghapus
        TaskLinkHistory::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'old_link' => $oldLink,
            'new_link' => null,
            'notes' => 'Link removed by user',
        ]);

        $task->link = null;
        $task->save();

        // Buat komentar
        $commentMessage = "**Link Removed**\n";
        $commentMessage .= "🗑️ Removed Link: " . $oldLink;
        
        $this->addCommentToTask($task, $commentMessage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Link removed successfully',
                'task' => $task->fresh(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Link removed successfully!');
    }

    /**
     * Get link history for a task.
     */
    public function getLinkHistory(Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to view link history for this task.');
        }

        $history = TaskLinkHistory::where('task_id', $task->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(Request $request, Task $task)
    {
        // Debug log
        Log::info('MarkAsCompleted called', [ // DIPERBAIKI: Gunakan Log::
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'task_assigned_to' => $task->assigned_to,
            'request_method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'ip' => $request->ip()
        ]);
        
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            abort(403, 'You are not authorized to update this task.');
        }

        $oldStatus = $task->status;
        $oldProgress = $task->progress;
        
        // Mark as completed
        $task->status = 'done';
        $task->progress = 100;
        $task->completed_at = now();
        $task->save();

        // Buat komentar
        $commentMessage = "**Task Completed**\n";
        $commentMessage .= "✅ Marked as completed by user\n";
        $commentMessage .= "📊 Progress: {$oldProgress}% → 100%\n";
        $commentMessage .= "📋 Status: " . ucfirst($oldStatus) . " → Done";

        $this->addCommentToTask($task, $commentMessage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task marked as completed',
                'task' => $task->fresh()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task marked as completed.');
    }

    /**
     * Alternative method to mark task as completed via GET (for debugging)
     */
    public function markCompleteGet(Task $task)
    {
        Log::info('MarkCompleteGet called via GET', [ // DIPERBAIKI: Gunakan Log::
            'user_id' => Auth::id(),
            'task_id' => $task->id
        ]);
        
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to update this task.');
        }

        $oldStatus = $task->status;
        $oldProgress = $task->progress;
        
        // Mark as completed
        $task->status = 'done';
        $task->progress = 100;
        $task->completed_at = now();
        $task->save();

        // Buat komentar
        $commentMessage = "**Task Completed via GET**\n";
        $commentMessage .= "✅ Marked as completed\n";
        $commentMessage .= "📊 Progress: {$oldProgress}% → 100%\n";
        $commentMessage .= "📋 Status: " . ucfirst($oldStatus) . " → Done";

        $this->addCommentToTask($task, $commentMessage);

        return redirect()->back()
            ->with('success', 'Task marked as completed via GET.');
    }

    /**
     * Add comment to task.
     */
    public function addComment(Request $request, Task $task)
    {
        $userId = Auth::id();
        
        if ($task->assigned_to !== $userId) {
            abort(403, 'You are not authorized to comment on this task.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:1000'
        ]);

        // Asumsi ada method addComment di model Task
        if (method_exists($task, 'addComment')) {
            $comment = $task->addComment($validated['content'], Auth::user());
        } else {
            // Fallback: buat comment manual
            $comment = $task->comments()->create([
                'user_id' => $userId,
                'content' => $validated['content'],
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added',
                'comment' => $comment->load('user')
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment added.');
    }

    /**
     * Get tasks with links.
     */
    public function tasksWithLinks()
    {
        $userId = Auth::id();

        $tasks = Task::where('assigned_to', $userId)
            ->whereNotNull('link')
            ->with(['project', 'linkHistories' => function($query) {
                $query->latest()->limit(5);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $linkStats = [
            'total_links' => Task::where('assigned_to', $userId)->whereNotNull('link')->count(),
            'recent_links' => Task::where('assigned_to', $userId)
                ->whereNotNull('link')
                ->where('updated_at', '>=', now()->subDays(7))
                ->count(),
            'links_by_domain' => $this->getLinksByDomain($userId),
        ];

        return view('member.tasks.links', compact('tasks', 'linkStats'));
    }

    /**
     * Get links grouped by domain.
     */
    private function getLinksByDomain($userId)
    {
        $tasks = Task::where('assigned_to', $userId)
            ->whereNotNull('link')
            ->get();

        $domains = [];
        foreach ($tasks as $task) {
            $parsedUrl = parse_url($task->link);
            if (isset($parsedUrl['host'])) {
                $domain = $parsedUrl['host'];
                if (strpos($domain, 'www.') === 0) {
                    $domain = substr($domain, 4);
                }
                
                if (!isset($domains[$domain])) {
                    $domains[$domain] = 0;
                }
                $domains[$domain]++;
            }
        }

        arsort($domains);
        return $domains;
    }

    /**
     * Get today's tasks for dashboard widget.
     */
    public function todaysTasks()
    {
        $userId = Auth::id();

        $tasks = Task::where('assigned_to', $userId)
            ->whereDate('deadline', today())
            ->where('status', '!=', 'done')
            ->orderBy('priority', 'desc')
            ->orderBy('deadline', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'count' => $tasks->count(),
        ]);
    }

    /**
     * Get task statistics.
     */
    public function stats()
    {
        $userId = Auth::id();

        $stats = [
            'total' => Task::where('assigned_to', $userId)->count(),
            'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
            'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
            'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
            'overdue' => Task::where('assigned_to', $userId)
                ->where('deadline', '<', now())
                ->where('status', '!=', 'done')
                ->count(),
            'due_today' => Task::where('assigned_to', $userId)
                ->whereDate('deadline', today())
                ->where('status', '!=', 'done')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Helper method to add comment to task.
     */
    private function addCommentToTask(Task $task, string $content)
    {
        if (method_exists($task, 'addComment')) {
            return $task->addComment($content, Auth::user());
        }
        
        return $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $content,
        ]);
    }
}