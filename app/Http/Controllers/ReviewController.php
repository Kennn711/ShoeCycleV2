<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reviews' => 'required|array',
            'reviews.*.shoe_id' => 'required|exists:shoes,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string|max:500',
        ]);

        foreach ($request->reviews as $reviewData) {
            // Gunakan updateOrCreate untuk mencegah ulasan ganda jika user mencoba submit ulang
            Reviews::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'transaction_id' => $request->transaction_id,
                    'shoe_id' => $reviewData['shoe_id'],
                ],
                [
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                ]
            );
        }

        return response()->json(['success' => true]);
    }
}
