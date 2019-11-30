<?php

namespace kissj\Participant\Admin;

use kissj\AbstractController;
use kissj\Participant\Guest\GuestService;
use kissj\Participant\Ist\IstService;
use kissj\Participant\Patrol\PatrolService;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminController extends AbstractController {
    public function showDashboard(
        Response $response,
        PatrolService $patrolService,
        IstService $istService,
        GuestService $guestService
    ) {
        return $this->view->render(
            $response,
            'admin/dashboard-admin.twig',
            [
                'patrols' => $patrolService->getAllPatrolsStatistics(),
                'ists' => $istService->getAllIstsStatistics(),
                'guests' => $guestService->getAllGuestsStatistics(),
            ]
        );
    }

    public function showApproving(Request $request, Response $response, array $args) {
        $closedPatrols = $this->patrolService->getAllClosedPatrols();
        $closedIsts = $this->istService->getAllClosedIsts();

        return $this->view->render($response, 'admin/approving-admin.twig', [
            'eventName' => 'CEJ 2018',
            'closedPatrols' => $closedPatrols,
            'closedIsts' => $closedIsts,
        ]);
    }

    public function showPayments(Request $request, Response $response, array $args) {
        $approvedPatrols = $this->patrolService->getAllApprovedPatrolsWithPayment();
        $approvedIsts = $this->istService->getAllApprovedIstsWithPayment();

        $this->view->render($response, 'admin/payments-admin.twig', [
            'eventName' => 'CEJ 2018',
            'approvedPatrols' => $approvedPatrols,
            'approvedIsts' => $approvedIsts,
        ]);
    }

    public function setPaymentPaid(Request $request, Response $response, array $args) {
        /** @var \kissj\Payment\PaymentService $paymentService */
        $paymentService = $this->get('paymentService');
        $paymentId = $args['payment'];
        $paymentService->setPaymentPaid($paymentService->getPaymentFromId($paymentId));
        $this->flashMessages->success('Platba je označená jako zaplacená, mail o zaplacení odeslaný');
        $this->logger->info('Payment with ID '.$paymentId.' is set as paid by hand');

        return $response->withRedirect($this->router->pathFor('admin-payments'));
    }
}