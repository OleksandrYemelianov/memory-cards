<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use App\Services\AppLangService;
use App\Services\CurrentGroupService;
use App\Services\GroupsService;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit test: every collaborator is a mock, so no database, no HTTP,
 * no framework boot. This is only possible because GroupsService receives
 * its dependencies through the constructor (was static helper calls before).
 */
class GroupsServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private GroupRepositoryInterface $groups;
    private MemoryCardRepositoryInterface $cards;
    private CurrentGroupService $currentGroup;
    private AppLangService $lang;
    private GroupsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groups       = Mockery::mock(GroupRepositoryInterface::class);
        $this->cards        = Mockery::mock(MemoryCardRepositoryInterface::class);
        $this->currentGroup = Mockery::mock(CurrentGroupService::class);
        $this->lang         = Mockery::mock(AppLangService::class);

        $this->service = new GroupsService(
            $this->groups,
            $this->cards,
            $this->currentGroup,
            $this->lang,
        );
    }

    public function test_returns_empty_array_when_no_language_resolved(): void
    {
        // No langId passed → falls back to AppLangService::getId(), which is 0.
        $this->lang->shouldReceive('getId')->once()->andReturn(0);
        $this->groups->shouldReceive('findAllByLanguage')->never();

        $this->assertSame([], $this->service->getGroups());
    }

    public function test_keeps_current_group_when_it_belongs_to_the_language(): void
    {
        $this->groups->shouldReceive('findAllByLanguage')
            ->once()->with(5)
            ->andReturn($this->groupCollection([10, 20, 30]));

        // Current group 20 is in the list → it stays, set() is not called.
        $this->currentGroup->shouldReceive('get')->once()->andReturn(20);
        $this->currentGroup->shouldReceive('set')->never();

        $result = $this->service->getGroups(5);

        $this->assertSame(20, $result['curr_group_id']);
        $this->assertCount(3, $result['groups']);
    }

    public function test_falls_back_to_first_group_when_current_is_stale(): void
    {
        $this->groups->shouldReceive('findAllByLanguage')
            ->once()->with(5)
            ->andReturn($this->groupCollection([10, 20, 30]));

        // Current group 99 is not in the list → first group (10) is selected
        // and persisted via CurrentGroupService::set().
        $this->currentGroup->shouldReceive('get')->once()->andReturn(99);
        $this->currentGroup->shouldReceive('set')->once()->with(10);

        $result = $this->service->getGroups(5);

        $this->assertSame(10, $result['curr_group_id']);
    }

    public function test_update_qty_pushes_counted_cards_into_repository(): void
    {
        $this->cards->shouldReceive('countByGroup')->once()->with(7)->andReturn(42);
        $this->groups->shouldReceive('updateQuantity')->once()->with(7, 42);

        $this->service->updateQty(7);
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function groupCollection(array $ids): Collection
    {
        return new Collection(
            array_map(fn (int $id) => ['id' => $id, 'name' => 'group_' . $id], $ids)
        );
    }
}
