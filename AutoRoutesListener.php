<?php

namespace Statamic\Addons\AutoRoutes;

use Statamic\API\Collection;
use Statamic\Extend\Listener;

class AutoRoutesListener extends Listener
{
    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        'cp.page.published' => 'updateRoute',
    ];

    private $entry;

    /**
     * Update routes.yaml if the slug of page with a mounted collection get's updated.
     */
    public function updateRoute($entry) 
    {
        $this->entry = $entry;

        $mount = $this->entry->get('mount');
        if($mount)
        {
            $url = $this->entry->url();
            $collection = Collection::whereHandle($mount);
            // Only update the routes if there are already routes defined for this collection.
            if($collection->route())
            {
                // Add routes for each locale.
                $locales = $this->entry->locales();
                foreach($locales as $locale) 
                {
                    $routes[$locale] = $this->entry->in($locale)->uri() . '/{slug}';
                }
                $collection->route($routes);
                $collection->save();
            }
        }
    }
}