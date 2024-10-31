<?php

namespace A21ns1g4ts\FilamentStripe\Http\Middleware;

use A21ns1g4ts\FilamentStripe\Models\Customer;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;

class LoadCustomer
{
    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function handle($request, Closure $next)
    {
        $billable = $this->auth->user();
        if ($billable) {
            $customer = Customer::whereBillableType('user')->whereBillableId($billable->id)->first();
            Session::put('customer', $customer);
            Session::put('billable', $billable);
        } else {
            Session::forget('customer');
            Session::forget('billable');
        }

        return $next($request);
    }
}
