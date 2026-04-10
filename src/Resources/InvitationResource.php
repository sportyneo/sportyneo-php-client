<?php

namespace Sportyneo\SDK\Resources;

class InvitationResource extends BaseResource
{
    protected $endpoint = '/invitations';

    /**
     * Envoie une invitation par e-mail à rejoindre un club (shop).
     * Nécessite une authentification Basic.
     *
     * @param string $email   Adresse e-mail du destinataire
     * @param int    $shopId  Identifiant du club
     * @return array          { message: string }
     */
    public function invite(string $email, int $shopId): array
    {
        return $this->client->post($this->endpoint, [
            'email'   => $email,
            'shop_id' => $shopId,
        ]);
    }

    /**
     * Vérifie la validité d'un token d'invitation (public, sans authentification).
     * Retourne les infos de l'invitation ou une erreur 404/410.
     *
     * @param string $token  Token de l'invitation (64 hex chars)
     * @return array         { email, entity_name, shop_name, inviter_name, expires_at }
     */
    public function verify(string $token): array
    {
        return $this->client->get($this->endpoint.'/'.$token);
    }

    /**
     * Accepte une invitation et crée le compte utilisateur avec le mot de passe choisi.
     * Public, sans authentification.
     *
     * @param string $token    Token de l'invitation
     * @param string $password Mot de passe choisi par l'utilisateur (min. 8 caractères)
     * @return array           { message: string }
     */
    public function accept(string $token, string $password): array
    {
        return $this->client->post($this->endpoint.'/'.$token.'/accept', [
            'password' => $password,
        ]);
    }
}
