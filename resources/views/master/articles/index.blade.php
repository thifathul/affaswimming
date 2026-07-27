<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                {{ __('Articles') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 rounded-full border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 shadow-sm flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
                <span class="info-badge"><i class="fa-solid fa-crown mr-1"></i> Hak Akses: {{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Articles</h3>
                        <p class="text-sm text-slate-500 mt-1">Manage news and updates for the landing page.</p>
                    </div>
                    <a href="{{ route('master.articles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-md shadow-slate-800/20 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95">
                        <i class="fa-solid fa-plus text-xs"></i> Create New Article
                    </a>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Article</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Author</th>
                                <th class="p-4">Published At</th>
                                <th class="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($articles as $article)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-4">
                                            <!-- Thumbnail Gambar -->
                                            <div class="w-24 h-16 rounded-xl flex-shrink-0 flex items-center justify-center bg-slate-100 border border-slate-150 overflow-hidden shadow-sm">
                                                @if($article->image)
                                                    <img src="{{ asset('storage/' . $article->image) }}" alt="" class="h-full w-full object-cover">
                                                @else
                                                    <i class="fa-regular fa-image text-slate-400 text-xl"></i>
                                                @endif
                                            </div>
                                            
                                            <!-- Teks Info -->
                                            <div class="max-w-[400px]">
                                                <p class="font-bold text-slate-800 leading-snug">{{ $article->title }}</p>
                                                <p class="text-xs text-slate-400 mt-1 line-clamp-1">{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 80) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($article->is_published)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                                Published
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-600 font-medium">
                                        {{ $article->user->name ?? 'Admin' }}
                                    </td>
                                    <td class="p-4 text-slate-500">
                                        {{ $article->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-4 pr-6">
                                        <div class="flex items-center justify-end gap-3.5">
                                            <a href="{{ route('master.articles.edit', $article->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors text-lg" title="Edit Article">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('master.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-lg bg-transparent border-none p-0 cursor-pointer" title="Delete Article">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fa-regular fa-folder-open text-5xl mb-4 text-slate-300"></i>
                                            <p class="text-base font-semibold">No articles found</p>
                                            <p class="text-xs text-slate-400 mt-1">Get started by creating a new article.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Copyright from Screenshot -->
                <div class="mt-12 text-center text-xs text-slate-400 font-medium">
                    © 2026 SMK Pasundan 1 Bandung.
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>
