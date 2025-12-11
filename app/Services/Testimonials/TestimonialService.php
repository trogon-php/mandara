<?php

namespace App\Services\Testimonials;

use App\Models\Testimonial;
use App\Services\Core\BaseService;
use App\Http\Resources\Testimonials\AppTestimonialResource;

class TestimonialService extends BaseService
{
    protected string $modelClass = Testimonial::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for the admin interface
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
            'rating' => [
                'type' => 'select',
                'label' => 'Rating',
                'col' => 3,
                'options' => [
                    '5' => '5 Stars',
                    '4' => '4 Stars',
                    '3' => '3 Stars',
                    '2' => '2 Stars',
                    '1' => '1 Star',
                ],
            ],
            'created_at' => [
                'type' => 'date-range',
                'label' => 'Date Range',
                'col' => 4,
                'fromField' => 'date_from',
                'toField' => 'date_to',
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'content' => 'Content',
            'user_name' => 'User Name',
            'designation' => 'Designation',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['content', 'user_name', 'designation'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get active testimonials for app
     */
    public function getAppTestimonials(): array
    {
        $testimonials = $this->model->whereStatus(1)->sorted()->limit(5)->get();
        return AppTestimonialResource::collection($testimonials)->toArray(request());
    }

    /**
     * Get featured testimonials
     */
    public function getFeaturedTestimonials(int $limit = 5): array
    {
        $testimonials = $this->model->where('status', 1)
            ->where('featured', 1)
            ->sorted()
            ->limit($limit)
            ->get();
        return (new AppTestimonialCollection($testimonials))->toArray(request());
    }

    /**
     * Get testimonials by rating
     */
    public function getTestimonialsByRating(int $rating): array
    {
        $testimonials = $this->model->where('status', 1)
            ->where('rating', $rating)
            ->sorted()
            ->get();
        return (new AppTestimonialCollection($testimonials))->toArray(request());
    }

    /**
     * Get high-rated testimonials (4+ stars)
     */
    public function getHighRatedTestimonials(): array
    {
        $testimonials = $this->model->where('status', 1)
            ->where('rating', '>=', 4)
            ->sorted()
            ->get();
        return (new AppTestimonialCollection($testimonials))->toArray(request());
    }
}