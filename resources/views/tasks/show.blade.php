@extends('tasks.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📄 Chi tiết công việc</h5>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">⬅ Quay lại</a>
            </div>
            <div class="card-body">
                <h4 class="fw-bold mb-3">{{ $task->title }}</h4>
                <p class="text-muted"><strong>Mô tả:</strong></p>
                <p>{{ $task->description }}</p>
                <p><strong>Hạn chót:</strong> <span class="badge bg-danger">{{ $task->due_date ? $task->due_date : 'Chưa thêm date' }}</span></p>
                <p><strong>Trạng thái:</strong> <td>
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
                    </td></p>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('tasks.edit', 1) }}" class="btn btn-warning me-2">✏️ Sửa</a>
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá công việc này?');" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑 Xoá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
