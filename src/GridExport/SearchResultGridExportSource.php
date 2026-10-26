<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\GridExport;

use OpenDxp\Bundle\AdminBundle\Attribute\AsGridExportSource;
use OpenDxp\Bundle\AdminBundle\GridExport\GridExportQuery;
use OpenDxp\Bundle\AdminBundle\GridExport\GridExportSourceInterface;
use OpenDxp\Bundle\AdminBundle\GridExport\SessionGridExportSourceInterface;
use OpenDxp\Bundle\AdminBundle\GridExport\Source\ObjectGridExportSource;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Installer;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Security\PermissionAttribute;

#[AsGridExportSource(
    name: 'advanced-object-search',
    permission: PermissionAttribute::PREFIX . Installer::PERMISSION_KEY,
    batchSize: 20,
)]
final class SearchResultGridExportSource implements GridExportSourceInterface, SessionGridExportSourceInterface
{
    public function __construct(
        private readonly ObjectGridExportSource $objects,
        private readonly Service $service,
    ) {
    }

    public function getSessionParameters(GridExportQuery $query): array
    {
        return $this->objects->getSessionParameters($query);
    }

    public function getColumns(GridExportQuery $query): array
    {
        return $this->objects->getColumns($this->createObjectQuery($query));
    }

    public function countRows(GridExportQuery $query): int
    {
        $objectQuery = $this->createObjectQuery($query);

        // The object grid exports every object of the class when it gets no IDs.
        if ($objectQuery->selectedIds === []) {
            return 0;
        }

        return $this->objects->countRows($objectQuery);
    }

    public function getRows(GridExportQuery $query, int $offset, int $limit): iterable
    {
        $objectQuery = $this->createObjectQuery($query);

        if ($objectQuery->selectedIds === []) {
            return [];
        }

        return $this->objects->getRows($objectQuery, $offset, $limit);
    }

    private function createObjectQuery(GridExportQuery $query): GridExportQuery
    {
        $search = json_decode((string) ($query->parameters['search'] ?? '{}'), true, flags: JSON_THROW_ON_ERROR);
        $classId = (string) ($search['classId'] ?? '');

        $ids = $query->selectedIds !== []
            ? $query->selectedIds
            : $this->service->getIdsFromFilterNoLimit(
                $classId,
                $search['conditions']['filters'] ?? [],
                $search['conditions']['fulltextSearchTerm'] ?? '',
            );

        return new GridExportQuery(
            $query->userId,
            $query->language,
            $query->timezone,
            [
                ...$query->parameters,
                'classId' => $classId,
                'folderId' => 1,
            ],
            $ids,
            $query->sessionParameters,
        );
    }
}
