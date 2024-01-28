<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Menu;

class MenuController extends Controller
{
    // Menampilkan semua menu
    public function index()
    {
        $menus = Menu::all();
        return view('menu.index', compact('menus'));
    }

    // Menampilkan form untuk menambahkan menu baru
    public function create()
    {
        return view('menu.create');
    }

    // Menyimpan menu baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'menu_name' => 'required',
            'order' => 'required|numeric',
        ]);

        Menu::create($request->all());

        return redirect()->route('menus.index')->with('success', 'Menu added successfully');
    }

    // Menampilkan detail menu
    public function show(Menu $menu)
    {
        return view('menu.show', compact('menu'));
    }

    // Menampilkan form untuk mengedit menu
    public function edit(Menu $menu)
    {
        return view('menu.edit', compact('menu'));
    }

    // Menyimpan perubahan pada menu ke database
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'menu_name' => 'required',
            'order' => 'required|numeric',
        ]);

        $menu->update($request->all());

        return redirect()->route('menus.index')->with('success', 'Menu updated successfully');
    }

    // Menghapus menu dari database
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully');
    }
}
