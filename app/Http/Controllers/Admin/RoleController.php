<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // 1. عرض صفحة إدارة المستخدمين وترقيتهم
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')
                ->latest('created_at')
                ->get();

            return response()->json([
                'data' => $users,
            ], 200);
        }

        return view('dashboard.admin.users.index');
    }

    // 2. تحديث دور/صلاحية المستخدم (User -> Moderator -> Admin)
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,moderator,admin',
        ]);

        $user = User::findOrFail($id);
        // منع تعديل صلاحيات المدير الأساسي
        // حماية: منع المدير من إلغاء صلاحية نفسه بنفسه
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إلغاء صلاحية الأدمن الخاصة بحسابك الحالي!',
            ], 422);
        }
        if ($user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'لا يمكن تغير صلاحية حساب المدير الرئيسي للنظام.',
                ], 403);
            }

            return redirect()->back()->with('error', 'لا يمكن تغير صلاحية حساب المدير الرئيسي للنظام.');
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صلاحية المستخدم بنجاح.',
        ]);
    }

    // 3. حظر / تفعيل حساب المستخدم
    public function toggleStatus($id, Request $request)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك تغيير حالة حسابك الحالي!',
            ], 422);
        }
        if ($user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حظر أو تفعيل أو تعديل حساب المدير الرئيسي للنظام.',
                ], 403);
            }

            return redirect()->back()->with('error', 'لا يمكن حظر أو تفعيل أو تعديل حساب المدير الرئيسي للنظام.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'تم تفعيل الحساب.' : 'تم حظر الحساب بنجاح.',
            'is_active' => $user->is_active,
        ]);
    }
}
