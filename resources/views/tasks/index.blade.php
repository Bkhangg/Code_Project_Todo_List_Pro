@extends('tasks.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách công việc</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">+ Thêm Task</a>
                @if (request()->has('search') || request()->has('status') || request()->has('sort_option'))
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-danger">x Xóa bộ lọc</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}" class="row mb-3">
                {{-- Search --}}
                <div class="col-md-4 mt-1">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tìm kiếm...">
                </div>

                {{-- Status --}}
                <div class="col-md-3 mt-1">
                    <select name="status" class="form-select">
                        <option value="">- Lọc theo trạng thái -</option>
                        <option value="0" @if (request('status') == '0') selected @endif>Chưa làm</option>
                        <option value="1" @if (request('status') == '1') selected @endif>Đang làm</option>
                        <option value="2" @if (request('status') == '2') selected @endif>Hoàn thành</option>
                    </select>
                </div>

                {{-- Sort By --}}
                <div class="col-md-3 mt-1">
                    <select name="sort_option" class="form-select">
                        <option value="">- Sắp xếp theo -</option>
                        <option value="due_date_desc" @if (request('sort_option') == 'due_date_desc') selected @endif>Deadline mới nhất</option>
                        <option value="due_date_asc" @if (request('sort_option') == 'due_date_asc') selected @endif>Deadline cũ nhất</option>
                        <option value="created_at_desc" @if (request('sort_option') == 'created_at_desc') selected @endif>Ngày tạo mới nhất</option>
                        <option value="created_at_asc" @if (request('sort_option') == 'created_at_asc') selected @endif>Ngày tạo cũ nhất</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="col-md-2 mt-1">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Công việc</th>
                        <th>Hạn chót</th>
                        <th>Trạng thái</th>
                        <th></th>
                        <th>Thời gian tạo</th>
                        <th>Người tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Demo static -->
                    @forelse ($tasks as $task)
                        <tr data-id="{{ $task->id }}">
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->due_date->format('d-m-Y') ? $task->due_date->format('d-m-Y') : 'Chưa thêm date' }}</td>
                            <td>
                                @switch($task->status)
                                    @case(0)
                                        <span class="badge bg-primary badge-status">Chưa bắt đầu</span>
                                    @break

                                    @case(1)
                                        <span class="badge bg-warning badge-status">Đang Làm</span>
                                    @break

                                    @case(2)
                                        <span class="badge bg-success badge-status">Hoàn thành</span>
                                    @break

                                    @default
                                        <span class="badge bg-primary">Đang bắt đầu</span>
                                    @break
                                @endswitch
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Đổi trạng thái
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item change-status" data-status="0">Chưa làm</a></li>
                                        <li><a class="dropdown-item change-status" data-status="1">Đang làm</a></li>
                                        <li><a class="dropdown-item change-status" data-status="2">Hoàn thành</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                {{ $task->created_at->format('d-m-Y') }}
                            </td>
                            <td>
                                {{ $task->user->name }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tasks.show', $task->id) }}"
                                    class="btn btn-sm btn-info text-white">Xem</a>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá công việc này?');"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 Xoá</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <td class="text-center" colspan="7">Không có tasks nào trong đây.</td>
                        @endforelse
                    </tbody>
                </table>
                <div class="center">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.change-status').click(function(e){
                    e.preventDefault()

                    const taskRow = $(this).closest('tr')
                    const taskId = taskRow.data('id')
                    const newStatus = $(this).data('status')

                    $.ajax({
                        url : `/tasks/${taskId}/status`,
                        type: 'PATCH',
                        data: {
                            status: newStatus,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response){
                            if(response.success) {
                                const badge = taskRow.find('.badge-status')
                                let badgeText = '';
                                let badgeClass = 'badge ';

                                switch (response.status) {
                                    case '0':
                                        badgeText = 'Chưa bắt đầu';
                                        badgeClass += 'bg-primary';
                                        break;
                                    case '1':
                                        badgeText = 'Đang làm';
                                        badgeClass += 'bg-danger';
                                        break;
                                    case '2':
                                        badgeText = 'Hoàn thành';
                                        badgeClass += 'bg-success';
                                        break;
                                }

                                badge.attr('class', badgeClass + ' badge-status').text(badgeText)

                                toastr.success(response.message)
                            }
                        },
                        error: function(error) {
                            toastr.error('Cập nhật trạng thái thất bại!')
                        }
                    })
                })
            })
        </script>
    @endpush
