<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TrackingFormRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackingController extends Controller
{
    public function track(TrackingFormRequest $request): JsonResponse
    {
        $ip = $request->getClientIp();
        $time = Carbon::now()->toDateTimeString();
        $userAgent = $request->header('user-agent');
        $location = $request->only(['lat', 'lng']);

        $cache = Cache::get('tracking_info', []);
        $cache[date('m/d/Y')][] = compact('location', 'userAgent', 'ip', 'time');

        Cache::forever('tracking_info', $cache);

        return response()
            ->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $data = Cache::get('tracking_info', []);

        if ($date = $request->get('date')) {
            $data = $data[$date] ?? [];
        }

        return response()
            ->json([
                'success' => true,
                'hits' => $data
            ]);
    }
}
