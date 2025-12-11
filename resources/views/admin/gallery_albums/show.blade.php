<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Gallery Album Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($item->thumbnail)
                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="img-fluid rounded">
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="mdi mdi-image-outline" style="font-size: 3rem;"></i>
                                <p>No thumbnail available</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Title:</strong></td>
                                <td>{{ $item->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td>{{ $item->description ?: 'No description' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if ($item->status)
                                        <span class="badge bg-success"><i class="mdi mdi-check"></i> Active</span>
                                    @else
                                        <span class="badge bg-danger"><i class="mdi mdi-close"></i> Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Images Count:</strong></td>
                                <td>{{ $item->images_count ?? 0 }} images</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $item->created_at->format('d-m-Y, g:i A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated:</strong></td>
                                <td>{{ $item->updated_at->format('d-m-Y, g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
