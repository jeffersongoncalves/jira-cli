<?php

namespace App\Commands\Board;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Board;
use App\Services\BoardService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'board:list
        {--project= : Filter by project key}';

    protected $description = 'List boards';

    public function handle(BoardService $boardService): int
    {
        return $this->handleJiraErrors(function () use ($boardService) {
            $response = $boardService->list($this->option('project'));
            $boards = array_map(
                fn (array $data) => Board::fromApi($data),
                $response['values'] ?? []
            );

            $rows = array_map(fn (Board $board) => [
                $board->id,
                $board->name,
                $board->type,
            ], $boards);

            $this->renderTable(['ID', 'Name', 'Type'], $rows);

            return self::SUCCESS;
        });
    }
}
