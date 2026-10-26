<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\AdvancedObjectSearchBundle\Tests\Feature\GridExport;

use OpenDxp\Bundle\AdminBundle\Service\Grid\GridExportService;
use OpenDxp\Bundle\AdminBundle\Test\GridExport\GridExportFile;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Service;
use OpenDxp\Bundle\AdvancedObjectSearchBundle\Tests\Factory\BookFactory;
use OpenDxp\Model\DataObject\ClassDefinition;
use OpenDxp\Model\User;
use OpenDxp\Test\Factory\UserFactory;
use OpenDxp\TestFoundation\Browser;
use OpenDxp\TestFoundation\Container;
use Zenstruck\Browser\KernelBrowser;

/**
 * Returns the parameters of a result grid. The grid searches the books for the term and shows their titles.
 *
 * @return array<string, mixed>
 */
function searchFor(string $term): array
{
    return [
        'search' => json_encode([
            'classId' => ClassDefinition::getByName('Book')->getId(),
            'conditions' => [
                'filters' => [],
                'fulltextSearchTerm' => $term,
            ],
        ], JSON_THROW_ON_ERROR),
        'language' => 'en',
        'fields' => ['title'],
        'columns' => [
            ['key' => 'title', 'label' => 'Title'],
        ],
    ];
}

/**
 * @param array<string, mixed> $parameters
 * @param list<int> $selectedIds
 */
function startSearchExport(User $user, array $parameters, array $selectedIds = []): KernelBrowser
{
    return Browser::actingAs($user)->post('/admin/grid-export/start', [
        'body' => [
            'source' => 'advanced-object-search',
            'parameters' => json_encode($parameters, JSON_THROW_ON_ERROR),
            'selectedIds' => $selectedIds,
            'language' => 'en',
            'timezone' => 'UTC',
        ],
    ]);
}

/**
 * @param array<string, mixed> $parameters
 * @param list<int> $selectedIds
 */
function exportSearch(User $user, array $parameters, array $selectedIds = []): GridExportFile
{
    $start = startSearchExport($user, $parameters, $selectedIds)->content();
    $id = json_decode($start, true, flags: JSON_THROW_ON_ERROR)['id'];

    $batchCount = Container::get(GridExportService::class)->findExport($id)->getBatchCount();
    for ($batch = 0; $batch < $batchCount; $batch++) {
        Browser::actingAs($user)->post('/admin/grid-export/batch', [
            'body' => [
                'id' => $id,
                'batch' => $batch,
            ],
        ]);
    }

    $download = Browser::actingAs($user)
        ->visit(sprintf('/admin/grid-export/download?id=%s', $id))
        ->client();

    return new GridExportFile('export.csv', $download->getInternalResponse()->getContent());
}

beforeEach(function () {
    $this->service = Container::get(Service::class);
    $this->bookClass = ClassDefinition::getByName('Book');
    $this->service->updateMapping($this->bookClass);

    $this->admin = UserFactory::new()
        ->admin()
        ->create();
    $this->books = [
        BookFactory::createOne(['title' => 'Ada Lovelace']),
        BookFactory::createOne(['title' => 'Ada Yonath']),
        BookFactory::createOne(['title' => 'Grace Hopper']),
    ];

    Container::get('opendxp.advanced_object_search.search-client')->refreshIndex([
        'index' => $this->service->getIndexName('Book'),
    ]);
});

afterEach(function () {
    $this->service->deleteIndex($this->bookClass);
});

it('exports the hits of the search', function () {
    $file = exportSearch($this->admin, searchFor('Ada'));

    expect($file->getColumn('Title'))->toEqualCanonicalizing(['Ada Lovelace', 'Ada Yonath']);
});

it('exports only the selected rows', function () {
    $file = exportSearch($this->admin, searchFor('Ada'), [$this->books[1]->getId()]);

    expect($file->getColumn('Title'))->toBe(['Ada Yonath']);
});

it('exports no row when the search has no hit', function () {
    $file = exportSearch($this->admin, searchFor('Turing'));

    expect($file->getColumn('Title'))->toBe([]);
});

it('refuses a user without the permission for the search', function () {
    $user = UserFactory::new()
        ->withPermissions('objects')
        ->create();

    startSearchExport($user, searchFor('Ada'))->assertStatus(403);
});
