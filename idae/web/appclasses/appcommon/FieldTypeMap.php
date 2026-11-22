<?php

declare(strict_types=1);

namespace AppCommon;

/**
 * Single source of truth for the French -> English field-type code migration.
 * Legacy French codes stay the canonical values stored in MongoDB
 * (appscheme_field_type, appscheme_field.codeAppscheme_field_type);
 * English codes are accepted aliases in the ClassApp switches and in
 * appconf/conf_install_go.php seed data. No data migration is performed.
 */
class FieldTypeMap {
	public const FR_EN = [
		'texte'        => 'text',
		'textelibre'   => 'longtext',
		'texteformate' => 'richtext',
		'valeur'       => 'value',
		'prix'         => 'price',
		'prix_precis'  => 'price_precise',
		'pourcentage'  => 'percent',
		'date'         => 'date',
		'heure'        => 'time',
		'phone'        => 'phone',
		'email'        => 'email',
		'bool'         => 'bool',
		'icon'         => 'icon',
		'color'        => 'color',
		'password'     => 'password',
		'codification' => 'code',
		'image'        => 'image',
	];
}
