# Configuration et énumérations

> **Pré-requis :** [Authentification](basic-usage.md#authentification)

---

## GET /v1/configuration

Retourne toutes les énumérations utilisées par l'API.

**Réponse (200 OK) :**

```json
{
  "payment_methods": { "1": "ALMAPAY", "2": "FLOAPAY", "3": "STRIPE", "4": "CB", "5": "CHECK", "6": "COD", "7": "RYFT", "8": "BANK_TRANSFER" },
  "instalments": { "1": "CB1XC", "2": "CB1XD", "3": "CB3X", "4": "CB4X", "10": "CB10X", "12": "CB12X" },
  "order_status": { "1": "CART", "2": "PENDING", "3": "PAID", "4": "CANCELLED", "5": "FAILED", "6": "REIMBURSED", "7": "ISSUED", "8": "SAVED" },
  "order_delivery_status": { "1": "PENDING", "2": "SHIPPED", "3": "DELIVERED" },
  "order_item_types": { "1": "PRODUCT", "2": "INSURANCE", "3": "FEES", "4": "FINANCIAL_FEES", "5": "DISCOUNT", "6": "COUPON", "7": "DONATION", "8": "ETICKET", "9": "DELIVERY" },
  "order_item_categories": { "1": "LICENSES", "2": "STAGES", "3": "AUTRES", "4": "BOUTIQUE", "5": "PLANNING", "6": "MERCHANDISING" }
}
```

---

## Modes de paiement (PaymentMethod)

| Valeur | Code API | Nom | Type |
|:------:|----------|-----|------|
| 1 | ALMAPAY | Alma | En ligne |
| 2 | FLOAPAY | Floa | En ligne |
| 3 | STRIPE | Stripe | En ligne |
| 4 | CB | Carte Bancaire | En ligne |
| 5 | CHECK | Chèque | Physique |
| 6 | COD | Espèces | Physique |
| 7 | RYFT | Ryft | En ligne |
| 8 | BANK_TRANSFER | Virement bancaire | Physique |

> Ces valeurs (clé numérique) sont celles attendues par le champ `allowed_payment_methods` lors de la [création d'une session de paiement](payments.md#modes-de-paiement-autorisés).

---

## Institutions de remise (DiscountInstitution)

Valeurs acceptées par le champ `allowed_institutions` lors de la [création d'une session de paiement](payments.md#institutions-de-remise). Chaque institution est identifiée par son **slug** (string).

| Slug | Nom | Portée |
|------|-----|--------|
| `pass_commune` | Pass Commune | `local` |
| `pass_region` | Pass Région | `regional` |
| `pass_region_rhone_alpes` | Pass Région Rhône-Alpes | `regional` |
| `pass_sport` | Pass Sport | `national` |

---

## Échéanciers de paiement (Instalment)

| Valeur | Code | Description | Nb paiements |
|:------:|------|-------------|:------------:|
| 1 | CB1XC | Paiement unique par carte | 1 |
| 2 | CB1XD | Paiement unique par prélèvement | 1 |
| 3 | CB3X | Paiement en 3 fois | 3 |
| 4 | CB4X | Paiement en 4 fois | 4 |
| 10 | CB10X | Paiement en 10 fois | 10 |
| 12 | CB12X | Paiement en 12 fois | 12 |

---

## Statuts de commande (OrderStatus)

| Valeur | Code | Description |
|:------:|------|-------------|
| 1 | CART | Panier en cours |
| 2 | PENDING | En attente de paiement |
| 3 | PAID | Payée |
| 4 | CANCELLED | Annulée |
| 5 | FAILED | Échouée |
| 6 | REIMBURSED | Remboursée |
| 7 | ISSUED | Émise |
| 8 | SAVED | Sauvegardée |

---

## Statuts de livraison (OrderDeliveryStatus)

| Valeur | Code | Description |
|:------:|------|-------------|
| 1 | PENDING | En attente d'expédition |
| 2 | SHIPPED | Expédiée |
| 3 | DELIVERED | Livrée |

---

## Types d'articles (OrderItemType)

| Valeur | Code | Description | Montant positif |
|:------:|------|-------------|:---------------:|
| 1 | PRODUCT | Produit | ✅ |
| 2 | INSURANCE | Assurance | ✅ |
| 3 | FEES | Frais | ✅ |
| 4 | FINANCIAL_FEES | Frais financiers | ✅ |
| 5 | DISCOUNT | Réduction | ❌ (déduction) |
| 6 | COUPON | Coupon | ❌ (déduction) |
| 7 | DONATION | Don | ✅ |
| 8 | ETICKET | E-Ticket | ✅ |
| 9 | DELIVERY | Livraison | ✅ |

---

## Catégories d'articles (OrderItemCategory)

| Valeur | Code | Description | Éligible aux remises |
|:------:|------|-------------|:--------------------:|
| 1 | LICENSES | Adhésions / Licences | ✅ |
| 2 | STAGES | Stages | ✅ |
| 3 | AUTRES | Autres | ❌ |
| 4 | BOUTIQUE | Boutique | ❌ |
| 5 | PLANNING | Planning | ❌ |
| 6 | MERCHANDISING | Merchandising | ❌ |
