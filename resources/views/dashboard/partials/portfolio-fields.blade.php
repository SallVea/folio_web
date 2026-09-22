@php $p = $portfolio ?? null; @endphp

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-muted font-medium block mb-1.5">Judul *</label>
        <input type="text" name="title" value="{{ $p->title ?? '' }}" required
               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
    </div>
    <div>
        <label class="text-xs text-muted font-medium block mb-1.5">Kategori</label>
        <input type="text" name="category" value="{{ $p->category ?? '' }}" placeholder="cth: Mobile App"
               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
    </div>
</div>

<div>
    <label class="text-xs text-muted font-medium block mb-1.5">Deskripsi</label>
    <textarea name="description" rows="3"
              class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">{{ $p->description ?? '' }}</textarea>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-muted font-medium block mb-1.5">URL Project</label>
        <input type="url" name="project_url" value="{{ $p->project_url ?? '' }}"
               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
    </div>
    <div>
        <label class="text-xs text-muted font-medium block mb-1.5">URL GitHub</label>
        <input type="url" name="github_url" value="{{ $p->github_url ?? '' }}"
               class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-violet transition">
    </div>
</div>

<div>
    <label class="text-xs text-muted font-medium block mb-1.5">Tech Stack (pisahkan dengan koma)</label>
    <input type="text" name="tech_stack_input" value="{{ $p && $p->tech_stack ? implode(', ', $p->tech_stack) : '' }}"
           placeholder="Kotlin, Jetpack Compose, Laravel"
           class="w-full bg-bg border border-border rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-violet transition">
</div>

<label class="flex items-center gap-2 text-sm text-muted">
    <input type="checkbox" name="is_featured" value="1" {{ ($p->is_featured ?? false) ? 'checked' : '' }}
           class="rounded bg-bg border-border text-violet focus:ring-violet">
    Tandai sebagai unggulan
</label>
