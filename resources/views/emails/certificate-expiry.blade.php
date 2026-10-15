<p>Halo {{ $certificate->user->name }},</p>
<p>Sertifikat <strong>{{ $certificate->title }}</strong> ({{ $certificate->issuer }}) akan kadaluarsa pada {{ $certificate->expiry_date?->format('d M Y') }}.</p>
<p>Mohon segera perbarui bila perlu.</p>
<p>— Folio</p>