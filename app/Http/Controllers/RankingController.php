<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rankings\CreateRankingRequest;
use App\Http\Requests\Rankings\DeleteRankingRequest;
use App\Http\Requests\Rankings\FinishRankingRequest;
use App\Http\Requests\Rankings\UpdateRankingRequest;
use App\Jobs\DownloadDataJob;
use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RankingController extends Controller
{
    //TODO change from pk -> slug
    public function show($id): View
    {
        $ranking = Ranking::query()
            ->with('user', 'songs')
            ->findOrFail($id);

        //if ranking is not complete && the ranking doesn't belong to auth user abort
        if (! $ranking->is_ranked && $ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }

        return view('rank.show', [
            'songs' => $ranking->songs,
            'ranking' => $ranking,
            'creator' => $ranking->user->name,
        ]);
    }

    public function create(CreateRankingRequest $request): JsonResponse
    {
        return response()->json([
            'redirect' => route('rank.show', [
                'id' => Ranking::start($request)->id,
            ]),
        ], 200);
    }

    public function edit($id): View
    {
        return view('rank.edit', [
            'ranking' => Ranking::query()
                ->with('songs')
                ->findOrFail($id),
        ]);
    }

    public function update(UpdateRankingRequest $request, $id): JsonResponse
    {
        Ranking::findOrFail($id)->update($request->validated());

        session()->flash('success', 'Ranking was succesfully updated!');

        return response()->json([
            'redirect' => route('rank.index'),
        ], 200);
    }

    public function finish(FinishRankingRequest $request): JsonResponse
    {
        Ranking::complete(collect($request->songs), $request->rankingId);

        session()->flash('success', 'Ranking was succesfully saved!');

        return response()->json([
            'redirect' => route('home'),
        ], 200);
    }

    public function delete(DeleteRankingRequest $request): JsonResponse
    {
        Ranking::findOrFail($request->rankingId)->delete();
        Song::where('ranking_id', $request->rankingId)->delete();

        return response()->json([
            'message' => 'Your ranking has been tossed into the void, never to return. Or will it?',
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with('songs', 'artist')
                ->withCount('songs')
                ->latest()
                ->paginate(5),
        ], 200);
    }

    public function pages(): JsonResponse
    {
        return response()->json([
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with('artist', 'songs')
                ->withCount('songs')
                ->latest()
                ->paginate(5),
        ], 200);
    }

    public function export(): JsonResponse
    {
        $rankings = Ranking::query()
            ->where('user_id', auth()->id())
            ->with('songs', 'artist')
            ->get();

        DownloadDataJob::dispatch($rankings, auth()->user());

        return response()->json([
            'success' => true,
            'message' => "Your download has started and will be emailed to you when completed!"
        ], 200);
    }
}
