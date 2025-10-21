<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title','LIKE','%' . $request->search . '%')
                ->orWhere('description','LIKE','%' . $request->search . '%');
            });
        }

        // Status searching
        if ($request->filled('status') && in_array($request->status,[
            Task::NOT_STARTED, Task::IN_PROGRESS, Task::COMPLETED,
        ])) {
            $query->where('status',$request->status);
        }

        // Sort Option
        switch($request->sort_option) {
            case 'due_date_asc':
                $query->orderBy('due_date','ASC');
            break;
            case 'due_date_desc':
                $query->orderBy('due_date','DESC');
            break;
            case 'created_at_asc':
                $query->orderBy('created_at','ASC');
            break;
            case 'created_at_desc':
                $query->orderBy('created_at','DESC');
            break;
        }

        $tasks = $query->with('user')->paginate(10)->appends($request->all());

        return view('tasks.index',compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = array_merge($request->all(),[
            'user_id' => Auth::id(),
        ]);

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Create new Task successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.show',compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.edit',compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Tìm task theo ID, nếu không có sẽ báo lỗi 404
        $task = Task::findOrFail($id);

        // Xác thực dữ liệu từ form
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:0,1,2',
        ]);

        // Cập nhật dữ liệu
        $task->update($validated);

        // Chuyển hướng về trang danh sách và thông báo thành công
        return redirect()->route('tasks.index')->with('success', 'Cập nhật công việc thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Đã xoá công việc thành công!');
    }

    // Cập nhật code thay đổi status bằng ajax
    public function updateStatus(Request $request, $id) {
        $validate = $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        $task = Task::findOrFail($id);

        $task->update([
            'status' => $validate['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công!',
            'status' => $task->status,
        ]);
    }
}
