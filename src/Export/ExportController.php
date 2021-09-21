<?php

declare(strict_types=1);

namespace kissj\Export;

use kissj\AbstractController;
use kissj\Event\Event;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function assert;

class ExportController extends AbstractController
{
    public function __construct(
        private ExportService $exportService,
    ) {
    }

    public function exportHealthData(Request $request, Response $response)
    {
        $event = $request->getAttribute('user')->event;
        assert($event instanceof Event);
        $csvRows = $this->exportService->healthDataToCSV($event);
        $this->logger->info('Exported health data about participants');

        return $this->exportService->outputCSVresponse($response, $csvRows, $event->slug . '_health');
    }

    public function exportPaidData(Request $request, Response $response)
    {
        $event = $request->getAttribute('user')->event;
        assert($event instanceof Event);
        $csvRows = $this->exportService->paidContactDataToCSV($event);
        $this->logger->info('Exported data about participants which paid');

        return $this->exportService->outputCSVresponse($response, $csvRows, $event->slug . '_paid');
    }

    public function exportFullData(Request $request, Response $response)
    {
        $event = $request->getAttribute('user')->event;
        assert($event instanceof Event);
        $csvRows = $this->exportService->allRegistrationDataToCSV($event);
        $this->logger->info('Exported FULL current data about participants');

        return $this->exportService->outputCSVresponse($response, $csvRows, $event->slug . '_full');
    }
}
