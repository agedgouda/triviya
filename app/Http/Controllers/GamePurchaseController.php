<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseGameProductRequest;
use App\Models\GameProduct;
use App\Models\GameProductPurchase;
use Inertia\Inertia;

class GamePurchaseController extends Controller
{
    public function index()
    {
        return Inertia::render('Purchase/GamePurchasePage', [
            'products' => GameProduct::where('is_active', true)->get(),
        ]);
    }

    public function store(PurchaseGameProductRequest $request)
    {
        $product = GameProduct::findOrFail($request->validated('game_product_id'));

        GameProductPurchase::create([
            'user_id' => $request->user()->id,
            'game_product_id' => $product->id,
        ]);

        $request->user()->increment('games_available', $product->game_count);

        return redirect()->route('games')->with(
            'flashMessage',
            "You now have {$request->user()->fresh()->games_available} games available!"
        );
    }
}
