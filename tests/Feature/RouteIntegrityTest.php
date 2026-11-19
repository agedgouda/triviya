<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteIntegrityTest extends TestCase
{
    public function test_every_app_route_points_to_an_existing_controller_method(): void
    {
        $missing = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();

            if (! str_starts_with($action, 'App\\') || ! str_contains($action, '@')) {
                continue;
            }

            [$controller, $method] = explode('@', $action);

            if (! method_exists($controller, $method)) {
                $missing[] = "{$route->uri()} -> {$action}";
            }
        }

        $this->assertSame([], $missing, 'Routes point to missing controller methods.');
    }
}
