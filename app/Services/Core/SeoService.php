<?php

namespace App\Services\Core;

use Illuminate\Support\Str;

class SeoService
{
    protected array $data = [
        'title'        => null,
        'site_suffix'  => null,   // e.g. " | YourBrand"
        'description'  => null,
        'keywords'     => null,
        'canonical'    => null,
        'robots'       => 'index,follow',
        'og_type'      => 'website',
        'og_image'     => null,
        'twitter_card' => 'summary_large_image',
        'hreflangs'    => [],     // ['en'=>'...','hi'=>'...']
        'meta_extra'   => [],     // [['name'=>'author','content'=>'...']]
        'schema'       => [],     // JSON-LD blocks (array of arrays)
    ];

    public function set(array $attrs): self
    {
        foreach ($attrs as $k => $v) {
            if (!is_null($v)) $this->data[$k] = $v;
        }
        return $this;
    }

    public function title(string $title, ?string $suffix = null): self
    {
        $this->data['title'] = Str::of($title)->limit(60, '…')->value();
        if ($suffix !== null) $this->data['site_suffix'] = $suffix;
        return $this;
    }

    public function description(?string $text): self
    {
        $this->data['description'] = $text
            ? Str::of(strip_tags($text))->limit(160, '…')->value()
            : null;
        return $this;
    }

    public function canonical(?string $url): self
    {
        $this->data['canonical'] = $url;
        return $this;
    }

    public function robots(string $val): self
    {
        $this->data['robots'] = $val; // e.g. 'noindex,nofollow'
        return $this;
    }

    public function og(string $type = 'website', ?string $image = null): self
    {
        $this->data['og_type'] = $type;
        if ($image) $this->data['og_image'] = $image;
        return $this;
    }

    public function twitter(string $card = 'summary_large_image'): self
    {
        $this->data['twitter_card'] = $card;
        return $this;
    }

    public function hreflangs(array $map): self
    {
        $this->data['hreflangs'] = $map;
        return $this;
    }

    public function addMeta(string $name, string $content): self
    {
        $this->data['meta_extra'][] = compact('name', 'content');
        return $this;
    }

    public function addSchema(array $jsonLd): self
    {
        $this->data['schema'][] = $jsonLd;
        return $this;
    }

    public function get(): object
    {
        return (object) $this->data;
    }
}
