<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\UserAddress;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->latest()->get();
        return view('pages.settings', compact('user', 'addresses'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada dan bukan URL eksternal
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Biodata diri berhasil diperbarui.');
    }

    public function updateSecurity(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $data = ['email' => $request->email];

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password saat ini tidak cocok.');
            }
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Akun & Keamanan berhasil diperbarui.');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'full_address' => 'required|string',
            'is_primary' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        
        $isPrimary = $request->has('is_primary');
        if ($isPrimary || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_primary' => false]);
            $isPrimary = true;
        }

        $user->addresses()->create([
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'full_address' => $request->full_address,
            'is_primary' => $isPrimary,
        ]);

        // Also update users.address if primary
        if ($isPrimary) {
            $user->update(['address' => $request->full_address]);
        }

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function destroyAddress(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        $address->delete();
        
        // If deleted address was primary, make the latest one primary
        if ($address->is_primary) {
            $newPrimary = auth()->user()->addresses()->latest()->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
                auth()->user()->update(['address' => $newPrimary->full_address]);
            } else {
                auth()->user()->update(['address' => null]);
            }
        }
        
        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setPrimaryAddress(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        
        auth()->user()->addresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);
        auth()->user()->update(['address' => $address->full_address]);
        
        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function updateBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:50',
            'bank_account' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
        ]);

        auth()->user()->update($request->only('bank_name', 'bank_account', 'bank_account_name'));

        return back()->with('success', 'Rekening bank berhasil diperbarui.');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
            'email_notifications' => 'nullable|boolean',
            'order_updates' => 'nullable|boolean',
        ]);

        $settings = auth()->user()->settings ?? [];
        $settings['theme'] = $request->theme;
        $settings['email_notifications'] = $request->has('email_notifications');
        $settings['order_updates'] = $request->has('order_updates');

        auth()->user()->update(['settings' => $settings]);

        return back()->with('success', 'Preferensi berhasil diperbarui.');
    }
}
