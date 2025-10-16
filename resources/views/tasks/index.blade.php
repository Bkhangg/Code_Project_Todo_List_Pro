@extends('tasks.layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách công việc</h5>
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">+ Thêm Task</a>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Công việc</th>
                    <th>Hạn chót</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <!-- Demo static -->
                @forelse ($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->due_date ? $task->due_date : 'Chưa thêm date' }}</td>
                    <td>
                        @switch($task->status)
                            @case(0)
                                <span class="badge bg-primary">Đang bắt đầu</span>
                                @break
                            @case(1)
                                <span class="badge bg-warning">Đang Làm</span>
                                @break
                            @case(2)
                                <span class="badge bg-success">Hoàn thành</span>
                                @break
                            @default
                                <span class="badge bg-primary">Đang bắt đầu</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-end">
                        <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-info text-white">Xem</a>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá công việc này?');" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">🗑 Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                    <td class="text-center" colspan="5">Không có tasks nào trong đây.</td>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
