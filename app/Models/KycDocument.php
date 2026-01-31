<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KycDocument extends Model
{
    protected $table = 'kyc_documents';

    protected $fillable = ['kyc_verification_id', 'type', 'path', 'nom_original'];

    public const TYPE_PIECE_IDENTITE = 'piece_identite';
    public const TYPE_PIECE_IDENTITE_RECTO = 'piece_identite_recto';
    public const TYPE_PIECE_IDENTITE_VERSO = 'piece_identite_verso';
    public const TYPE_PHOTO_IDENTITE = 'photo_identite';
    public const TYPE_JUSTIFICATIF_DOMICILE = 'justificatif_domicile';
    public const TYPE_AUTRE = 'autre';

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(KycVerification::class);
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }

    public static function types(): array
    {
        return [
            self::TYPE_PIECE_IDENTITE => 'Pièce d\'identité',
            self::TYPE_PIECE_IDENTITE_RECTO => 'Pièce d\'identité (recto)',
            self::TYPE_PIECE_IDENTITE_VERSO => 'Pièce d\'identité (verso)',
            self::TYPE_PHOTO_IDENTITE => 'Photo d\'identité',
            self::TYPE_JUSTIFICATIF_DOMICILE => 'Justificatif de domicile',
            self::TYPE_AUTRE => 'Autre',
        ];
    }
}
