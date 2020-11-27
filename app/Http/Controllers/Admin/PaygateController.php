<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Paygates\PaygateRepositoryInterface;
use Illuminate\Http\Request;

class PaygateController extends Controller
{
    /**
     * Define global variable
     *
     * @var PaygateRepositoryInterface
     */
    protected $payGateRepository;

    /**
     * PaygateController constructor.
     * @param PaygateRepositoryInterface $payGateRepository
     */
    public function __construct(PaygateRepositoryInterface $payGateRepository)
    {
        $this->payGateRepository = $payGateRepository;
    }

    /**
     * Function get list pay gate
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Pay Gates list';

        return view('admin.pages.paygates.index', compact('title'));
    }
}
