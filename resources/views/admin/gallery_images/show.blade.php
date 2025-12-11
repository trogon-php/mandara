<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Gallery Image Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($item->image)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title ?: 'Gallery Image' }}" class="img-fluid rounded">
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="mdi mdi-image-outline" style="font-size: 3rem;"></i>
                                <p>No image available</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Title:</strong></td>
                                <td>{{ $item->title ?: 'Untitled Image' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td>{{ $item->description ?: 'No description' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Album:</strong></td>
                                <td>
                                    @if($item->album)
                                        <span class="badge bg-primary">{{ $item->album->title }}</span>
                                    @else
                                        <span class="text-muted">No album assigned</span>
                                    @endif
                                </td>
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
