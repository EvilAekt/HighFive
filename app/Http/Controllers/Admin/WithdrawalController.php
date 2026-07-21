<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user')->latest()->get();
        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed',
        ]);

        $withdrawal->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status penarikan dana berhasil diperbarui.');
    }
}
