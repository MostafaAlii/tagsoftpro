<div class="d-flex justify-content-center">
    <!-- زرار تعديل -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
        data-bs-target="#editClientModal{{ $client->id }}">
        <i class="fas fa-edit"></i>
    </button>
    
    <!-- Modal Edit -->
    <div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1"
        aria-labelledby="editClientLabel{{ $client->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editClientLabel{{ $client->id }}">تعديل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
    
                        <div class="mb-3">
                            <label class="form-label">اسم</label>
                            <input type="text" name="name" class="form-control" value="{{ $client->name }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" value="{{ $client->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control" value="{{ $client->phone }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                @foreach(\App\Enums\Client\ClientStatus::cases() as $status)
                                    <option value="{{ $status->value }}" 
                                        {{ $client->status === $status ? 'selected' : '' }}>
                                        {{ trans('dashboard/general.' . strtolower($status->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
    
    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تحديث</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Button (Optional) -->
    <button type="button" class="mx-1 btn btn-danger btn-sm" data-bs-toggle="modal"
        data-bs-target="#deleteModal{{ $client->id }}">
        <i class="fas fa-trash"></i>
    </button>

    <div class="modal fade" id="deleteModal{{ $client->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="text-center modal-body">
                    <p>هل أنت متأكد من حذف "<strong>{{ $client->name }}</strong>"؟</p>
                    <p class="text-danger">هذا الإجراء لا يمكن التراجع عنه.</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">نعم، حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>