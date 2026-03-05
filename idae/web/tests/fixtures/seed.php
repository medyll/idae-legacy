<?php
declare(strict_types=1);

/**
 * seed.php — Test fixture data for Idae PHPUnit suite
 *
 * Provides a minimal, self-consistent dataset that mirrors the production
 * appscheme + sample entity records. Call seed() from TestCase::seedFixtures().
 *
 * ALL writes go to the `idae_test` database on the mongo-test sidecar.
 * This file must never be loaded outside a MONGO_ENV=test context.
 *
 * @package Idae\Tests\Fixtures
 * Date: 2026-03-02
 */

namespace Idae\Tests\Fixtures;

use MongoDB\Database;

/**
 * Populate the test database with baseline fixture documents.
 *
 * @param Database $db  The `idae_test` database on the sidecar.
 */
function seed(Database $db): void
{
    // ------------------------------------------------------------------
    // appscheme — entity definitions
    // ------------------------------------------------------------------
    $db->selectCollection('appscheme')->drop();
    $db->selectCollection('appscheme')->insertMany([
        [
            'idappscheme'   => 1,
            'codeAppscheme' => 'produit',
            'nomAppscheme'  => 'Produit',
            'hasTypeScheme' => 0,
            'actif'         => 1,
        ],
        [
            'idappscheme'   => 2,
            'codeAppscheme' => 'agent',
            'nomAppscheme'  => 'Agent',
            'hasTypeScheme' => 0,
            'actif'         => 1,
        ],
    ]);

    // ------------------------------------------------------------------
    // appscheme_field — reusable field catalog
    // ------------------------------------------------------------------
    $db->selectCollection('appscheme_field')->drop();
    $db->selectCollection('appscheme_field')->insertMany([
        [
            'idappscheme_field'      => 1,
            'codeAppscheme_field'    => 'nom',
            'labelAppscheme_field'   => 'Nom',
            'typeAppscheme_field'    => 'text',
            'required'               => 1,
        ],
        [
            'idappscheme_field'      => 2,
            'codeAppscheme_field'    => 'description',
            'labelAppscheme_field'   => 'Description',
            'typeAppscheme_field'    => 'textarea',
            'required'               => 0,
        ],
        [
            'idappscheme_field'      => 3,
            'codeAppscheme_field'    => 'prix',
            'labelAppscheme_field'   => 'Prix',
            'typeAppscheme_field'    => 'prix',
            'required'               => 0,
        ],
    ]);

    // ------------------------------------------------------------------
    // appscheme_has_field — per-entity field binding
    // (field naming rule: codeAppscheme_field + ucfirst(codeAppscheme))
    // e.g. nom + produit → nomProduit
    // ------------------------------------------------------------------
    $db->selectCollection('appscheme_has_field')->drop();
    $db->selectCollection('appscheme_has_field')->insertMany([
        ['idappscheme' => 1, 'idappscheme_field' => 1, 'ordre' => 1],
        ['idappscheme' => 1, 'idappscheme_field' => 2, 'ordre' => 2],
        ['idappscheme' => 1, 'idappscheme_field' => 3, 'ordre' => 3],
        ['idappscheme' => 2, 'idappscheme_field' => 1, 'ordre' => 1],
    ]);

    // ------------------------------------------------------------------
    // produit — sample entity records
    // ------------------------------------------------------------------
    $db->selectCollection('produit')->drop();
    $db->selectCollection('produit')->insertMany([
        [
            'idproduit'          => 1,
            'nomProduit'         => 'Widget A',
            'descriptionProduit' => 'First test product',
            'prixProduit'        => 9.99,
            'actif'              => 1,
        ],
        [
            'idproduit'          => 2,
            'nomProduit'         => 'Widget B',
            'descriptionProduit' => 'Second test product',
            'prixProduit'        => 19.99,
            'actif'              => 1,
        ],
        [
            'idproduit'          => 3,
            'nomProduit'         => 'Widget C (inactive)',
            'descriptionProduit' => 'Inactive product for filter tests',
            'prixProduit'        => 0.0,
            'actif'              => 0,
        ],
    ]);

    // ------------------------------------------------------------------
    // agent — sample agent records (minimal, for auth/permission tests)
    // ------------------------------------------------------------------
    $db->selectCollection('agent')->drop();
    $db->selectCollection('agent')->insertMany([
        [
            'idagent'    => 1,
            'nomAgent'   => 'Test Admin',
            'loginAgent' => 'admin',
            'mdpAgent'   => password_hash('testpass', PASSWORD_BCRYPT),
            'ADMIN'      => 1,
            'DEV'        => 1,
            'actif'      => 1,
        ],
        [
            'idagent'    => 2,
            'nomAgent'   => 'Test User',
            'loginAgent' => 'user',
            'mdpAgent'   => password_hash('testpass', PASSWORD_BCRYPT),
            'ADMIN'      => 0,
            'DEV'        => 0,
            'actif'      => 1,
        ],
    ]);
}
