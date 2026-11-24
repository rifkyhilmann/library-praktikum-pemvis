<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Menampilkan data buku dengan filter search dan kategori
     * Contoh: GET /api/books?search=laskar&kategori=fiksi
     */
    public function index(Request $request)
    {
        try {
            $query = Book::query();

            // Filter pencarian berdasarkan judul atau penulis
            if ($request->filled('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan kategori (enum)
            if ($request->filled('kategori')) {
                $query->where('kategori', $request->query('kategori'));
            }

            $books = $query->latest()->get();

            return ApiResponse::success('Books retrieved successfully', $books);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Menyimpan data buku baru (POST /api/books)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul'        => ['required', 'string', 'max:150'],
                'penulis'      => ['required', 'string', 'max:100'],
                'kategori'     => ['nullable', 'in:fiksi,non-fiksi,teknologi,sains,sejarah,lainnya'],
                'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:' . date('Y')],
                'stok'         => ['nullable', 'integer', 'min:0'],
                'deskripsi'    => ['nullable', 'string'],
                'cover_url'    => ['nullable', 'string'],
            ]);

            $book = Book::create([
                'judul'        => $validated['judul'],
                'penulis'      => $validated['penulis'],
                'kategori'     => $validated['kategori'] ?? 'lainnya',
                'tahun_terbit' => $validated['tahun_terbit'],
                'stok'         => $validated['stok'] ?? 0,
                'deskripsi'    => $validated['deskripsi'] ?? null,
                'cover_url'    => $validated['cover_url'] ?? null,
            ]);

            return ApiResponse::success('Book created successfully', $book, 201);

        } catch (ValidationException $e) {
            return ApiResponse::error('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Menampilkan detail satu buku berdasarkan ID (GET /api/books/{id})
     */
    public function show(string $id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return ApiResponse::error('Book not found', null, 404);
            }

            return ApiResponse::success('Book retrieved successfully', $book);

        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Memperbarui data buku berdasarkan ID (PUT /api/books/{id})
     */
    public function update(Request $request, string $id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return ApiResponse::error('Book not found', null, 404);
            }

            $validated = $request->validate([
                'judul'        => ['sometimes', 'required', 'string', 'max:150'],
                'penulis'      => ['sometimes', 'required', 'string', 'max:100'],
                'kategori'     => ['sometimes', 'in:fiksi,non-fiksi,teknologi,sains,sejarah,lainnya'],
                'tahun_terbit' => ['sometimes', 'required', 'integer', 'min:1000', 'max:' . date('Y')],
                'stok'         => ['sometimes', 'integer', 'min:0'],
                'deskripsi'    => ['nullable', 'string'],
                'cover_url'    => ['nullable', 'string'],
            ]);

            $book->update($validated);

            return ApiResponse::success('Book updated successfully', $book);

        } catch (ValidationException $e) {
            return ApiResponse::error('Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

    /**
     * Menghapus buku berdasarkan ID (DELETE /api/books/{id})
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return ApiResponse::error('Book not found', null, 404);
            }

            $book->delete();

            return ApiResponse::success('Book deleted successfully', null);

        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }
}
