# Invitations

> L'envoi d'invitation nécessite une [authentification](basic-usage.md#authentification). Les endpoints `verify` et `accept` sont **publics**.

Le système d'invitation permet d'inviter un utilisateur à rejoindre un club (shop) spécifique. L'invité reçoit un email avec un lien valable **24 heures**. En acceptant, il crée son compte (ou met à jour son mot de passe) et obtient l'accès au club.

---

## POST /v1/invitations

Envoie une invitation par email.

> Si une invitation en attente existe déjà pour cet email et ce club, elle est remplacée.

```php
$client->invitations->invite('nouveau@example.com', $shopId);
```

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `email` | email | ✅ | Adresse email du destinataire |
| `shop_id` | integer | ✅ | Identifiant du club |

**Réponse (201 Created) :**

```json
{ "message": "Invitation envoyée." }
```

---

## GET /v1/invitations/{token}

Vérifie la validité d'un token et retourne les infos de l'invitation.  
**Public — aucune authentification requise.**

```php
$client->invitations->verify($token);
```

**Réponse (200 OK) :**

```json
{
    "email": "nouveau@example.com",
    "entity_name": "Fédération Sportive",
    "shop_name": "Club Olympique",
    "inviter_name": "Jean Dupont",
    "expires_at": "2024-06-11T10:00:00.000000Z"
}
```

**Codes d'erreur :**

| Code | `code` | Description |
|:----:|--------|-------------|
| 404 | — | Token introuvable |
| 410 | `invitation_expired` | Invitation expirée |
| 410 | `already_accepted` | Invitation déjà acceptée |

---

## POST /v1/invitations/{token}/accept

Accepte l'invitation et crée le compte utilisateur.  
**Public — aucune authentification requise.**

Si l'utilisateur existe déjà (même email), son mot de passe est mis à jour. L'accès au club est ajouté dans tous les cas.

```php
$client->invitations->accept($token, 'motdepasse123');
```

| Champ | Type | Requis | Description |
|-------|------|:------:|-------------|
| `password` | string | ✅ | Mot de passe choisi (min. 8 caractères) |

**Réponse (200 OK) :**

```json
{ "message": "Invitation acceptée. Vous pouvez maintenant vous connecter." }
```

**Codes d'erreur :** identiques à `verify`.

---

## Flux complet

```
1. POST /invitations              → Votre système envoie l'invitation
2. Email reçu par l'invité        → Lien vers votre interface (avec le token)
3. GET  /invitations/{token}      → Votre interface vérifie le token et affiche les infos
4. POST /invitations/{token}/accept → L'invité choisit son mot de passe → compte créé
5. Connexion                      → L'invité peut se connecter avec email + mot de passe
```
