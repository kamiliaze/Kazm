## 📸 Guide d'implémentation des photos de produits

### 🎯 Fonctionnalités implémentées

✅ **Upload de photos** : Chaque produit peut avoir une image
✅ **Affichage des images** : Les images s'affichent dans la liste des produits
✅ **Page détails** : Nouvelle page pour afficher le produit en détail avec sa photo
✅ **Gestion d'images** : Suppression automatique de l'ancienne image lors de la modification
✅ **Validation** : Vérification du type et de la taille du fichier (max 5MB)
✅ **Formats supportés** : JPG, PNG, WEBP

---

### 📁 Structure des fichiers

```
public/
└── images/
    └── products/          # Dossier pour stocker les images des produits
        └── .gitkeep

src/
├── Form/
│   └── ProductType.php    # ✏️ Formulaire avec nouveau champ FileType
└── Controller/
    └── ProductController.php  # ✏️ Gestion de l'upload et suppression d'images

templates/
├── product/
│   ├── index.html.twig    # ✏️ Affichage des produits avec images
│   ├── new.html.twig      # ✏️ Formulaire de création avec upload
│   ├── edit.html.twig     # ✏️ Formulaire de modification avec upload
│   └── show.html.twig     # 🆕 Détail du produit avec grande image
```

---

### 🚀 Comment utiliser

#### 1️⃣ **Ajouter un produit avec photo**

- Allez sur "➕ Nouveau Produit"
- Remplissez tous les champs du formulaire
- Sélectionnez une image JPG, PNG ou WEBP (max 5MB)
- Cliquez sur "Enregistrer le produit"

#### 2️⃣ **Modifier la photo d'un produit**

- Cliquez sur "✏️ Modifier" sur un produit
- Sélectionnez une nouvelle image (optionnel)
- Cliquez sur "Enregistrer les modifications"
- L'ancienne image sera supprimée automatiquement

#### 3️⃣ **Voir les détails d'un produit**

- Cliquez sur la carte du produit ou sur "👁️ Voir"
- La page affichera l'image en grand + tous les détails
- Vous pouvez éditer ou supprimer le produit depuis cette page

---

### 🔧 Modifications apportées

#### **ProductType.php**
- Changement du champ `image` de `ChoiceType` à `FileType`
- Ajout de validation : JPG, PNG, WEBP uniquement, max 5MB
- Messages d'aide en français

#### **ProductController.php**
- Méthode `new()` : Gère l'upload et crée le dossier s'il n'existe pas
- Méthode `edit()` : Upload avec suppression de l'ancienne image
- Utilisation de `ParameterBagInterface` pour obtenir le chemin du projet

#### **Templates**
- `index.html.twig` : Lien vers la page de détails
- `new.html.twig` : Formulaire stylisé avec enctype multipart
- `edit.html.twig` : Affiche l'image actuelle avant modification
- `show.html.twig` : 🆕 Page de détails professionnelle avec grande image

---

### ✨ Points forts de l'implémentation

- 🔒 Sécurité : Validation des fichiers (type et taille)
- 🗑️ Nettoyage : Suppression automatique des images inutilisées
- 🎨 Design : Placeholder avec initiales si pas d'image
- 📱 Responsive : Fonctionne sur tous les appareils
- 🚀 Performance : Images optimisées avec noms uniques (uniqid)
- 🇫🇷 Français : Tous les messages sont en français

---

### 📝 Notes importantes

1. Le dossier `public/images/products/` a été créé automatiquement
2. Les images sont stockées avec un nom unique (uniqid) pour éviter les conflits
3. Seuls les admins peuvent créer/modifier/supprimer les produits
4. Les images sont affichées en responsive (s'adaptent à l'écran)

