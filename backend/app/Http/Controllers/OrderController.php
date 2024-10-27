<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // public function index()
    // {
    //     $orders = Order::where('id_user', Auth::id())->with('orderItems.product')->get();
    //     return response()->json(['data' => $orders]);
    // }

    public function all()
    {
        $orders = Order::all();
        return response()->json(['data' => $orders]);
    }

    public function store(Request $request)
    {
        // Validasi untuk alamat dan no_hp
        $request->validate([
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:100',
            'metode_pembayaran' => 'required|in:transfer,cash',
            // Validasi nama_akun hanya jika metode_pembayaran adalah 'transfer'
            'nama_akun' => $request->metode_pembayaran === 'transfer' ? 'required|string|max:100' : 'nullable',
        ]);

        $cart = Cart::where('id_user', Auth::id())->first();

        $order = Order::create([
            'id_user' => Auth::id(),
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'no_inv' => 'INV-' . time(),
            'status_konfirmasi' => 'Menunggu',
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_akun' => $request->metode_pembayaran === 'transfer' ? $request->nama_akun : null, // Simpan nama_akun jika transfer
            'total' => 0,
            'id_cart' => $cart->id, // Simpan id_cart dari keranjang
        ]);

        $order->save();

        $total = 0;
        $carts = Cart::where('id_user', Auth::id())->get();

        foreach ($carts as $cartItem) {
            OrderItem::create([
                'id_order' => $order->id,
                'id_product' => $cartItem->id_product,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);

            $total += $cartItem->quantity * $cartItem->product->price;
        }

        $order->update(['total' => $total]);
        // Cart::where('id_user', Auth::id())->delete();

        // Kirim data ke WhatsApp
        // $this->sendWhatsAppMessage($order, $total);

        // DB::commit();

        

        return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $order]);
    }

    public function show()
    {
        if (!Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $idUser = Auth::id();

        $order = Order::where("id_user", $idUser)->where('status_konfirmasi', 'menunggu')->get();

        if (!$order) {
            return response()->json(['data' => ['id' => 0]]);
        }

        return response()->json(['data' => $order->load('orderItems.product')]);
    }

    private function sendWhatsAppMessage($order, $total)
    {
        $adminPhoneNumber = '082235418439'; // Ganti dengan nomor telepon admin
        $message = "Pesanan baru telah dibuat dengan detail sebagai berikut:\n\n";
        $message .= "Nomor Invoice: " . $order->no_inv . "\n";
        if ($order->metode_pembayaran === 'transfer') {
            $message .= "Nama Akun: " . $order->nama_akun . "\n"; // Tampilkan nama_akun hanya jika metode pembayaran transfer
        }
        $message .= "Nama Pelanggan: " . Auth::user()->email . "\n";
        $message .= "Alamat: " . $order->alamat . "\n";
        $message .= "No HP: " . $order->no_hp . "\n";
        $message .= "Total: Rp " . number_format($total, 0, ',', '.') . "\n";
        $message .= "Metode Pembayaran: " . $order->metode_pembayaran . "\n";
        $message .= "Status: " . $order->status_konfirmasi . "\n";

        // Kirim pesan menggunakan API WhatsApp
        Http::post('https://api.whatsapp/send', [
            'phone' => $adminPhoneNumber,
            'message' => $message,
        ]);
    }
}
