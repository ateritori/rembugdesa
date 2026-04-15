<?php

namespace App\Services\Borda;

class BordaPipelineService
{
    protected $groupService;
    protected $systemService;
    protected $finalService;

    public function __construct(
        GroupBordaAggregationService $groupService,
        SystemBordaAggregationService $systemService,
        FinalBordaAggregationService $finalService
    ) {
        $this->groupService = $groupService;
        $this->systemService = $systemService;
        $this->finalService = $finalService;
    }

    public function run($session, $method)
    {
        // group
        $this->groupService->calculate($session, $method, 'partisipatif');
        $this->groupService->calculate($session, $method, 'strategis');

        // system
        $this->systemService->calculate($session, $method);

        // final
        $this->finalService->calculate($session, $method);
    }
}
