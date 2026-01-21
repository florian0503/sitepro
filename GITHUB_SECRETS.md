# 🔐 Configuration des Secrets GitHub

Ce document explique comment configurer les secrets nécessaires pour le pipeline CI/CD.

## 📍 Où configurer les secrets ?

Allez dans votre repository GitHub :

```
Settings → Secrets and variables → Actions → New repository secret
```

## 🔑 Secrets à configurer

### 1. SSH_PRIVATE_KEY

**Description** : Clé privée SSH pour se connecter au serveur Hostinger

**Comment l'obtenir :**

```bash
# Option 1 : Générer une nouvelle paire de clés dédiée au déploiement
ssh-keygen -t rsa -b 4096 -C "deploy@github-actions" -f ~/.ssh/deploy_key

# Afficher la clé privée (à copier dans GitHub)
cat ~/.ssh/deploy_key

# Copier la clé publique sur Hostinger
ssh-copy-id -i ~/.ssh/deploy_key.pub -p 65002 u123456789@srv123456.hostinger.com
```

**OU**

```bash
# Option 2 : Utiliser votre clé SSH existante
cat ~/.ssh/id_rsa
```

**Valeur à copier dans GitHub** :
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
...
(tout le contenu de la clé privée)
...
-----END OPENSSH PRIVATE KEY-----
```

**Important** :
- Copiez TOUTE la clé, y compris les lignes `BEGIN` et `END`
- N'ajoutez PAS d'espaces ou de retours à la ligne supplémentaires

---

### 2. SSH_KNOWN_HOSTS

**Description** : Fingerprint SSH du serveur Hostinger pour éviter les warnings de connexion

**Comment l'obtenir :**

```bash
# Remplacez par votre serveur Hostinger
ssh-keyscan -p 65002 srv123456.hostinger.com
```

**Exemple de sortie** :
```
# srv123456.hostinger.com:65002 SSH-2.0-OpenSSH_8.2p1 Ubuntu-4ubuntu0.5
srv123456.hostinger.com ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC8...
srv123456.hostinger.com ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIK...
srv123456.hostinger.com ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlz...
```

**Valeur à copier dans GitHub** :
```
srv123456.hostinger.com ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC8...
srv123456.hostinger.com ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIK...
srv123456.hostinger.com ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlz...
```

**Important** :
- Copiez toutes les lignes retournées
- Ne modifiez pas le format

---

## ✅ Vérification de la configuration

Une fois les secrets configurés, vous pouvez les vérifier :

1. Allez dans **Settings → Secrets and variables → Actions**
2. Vous devez voir :
   - `SSH_PRIVATE_KEY`
   - `SSH_KNOWN_HOSTS`

3. Créez un commit et poussez sur `main` :
   ```bash
   git add .
   git commit -m "test: vérification du pipeline CI/CD"
   git push origin main
   ```

4. Allez dans l'onglet **Actions** de votre repository
5. Vérifiez que le workflow se lance et passe avec succès

---

## 🔧 Troubleshooting

### Erreur : "Permission denied (publickey)"

**Problème** : La clé publique n'est pas sur le serveur

**Solution** :
```bash
# Copier la clé publique sur le serveur
ssh-copy-id -i ~/.ssh/deploy_key.pub -p 65002 u123456789@srv123456.hostinger.com

# OU manuellement :
# 1. Se connecter au serveur
ssh -p 65002 u123456789@srv123456.hostinger.com

# 2. Créer le dossier .ssh s'il n'existe pas
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# 3. Ajouter la clé publique
nano ~/.ssh/authorized_keys
# Coller le contenu de deploy_key.pub

# 4. Corriger les permissions
chmod 600 ~/.ssh/authorized_keys
```

---

### Erreur : "Host key verification failed"

**Problème** : Le secret `SSH_KNOWN_HOSTS` est mal configuré

**Solution** :
```bash
# Regénérer le fingerprint
ssh-keyscan -p 65002 srv123456.hostinger.com

# Copier TOUTE la sortie dans le secret GitHub
```

---

### Erreur : "Load key: invalid format"

**Problème** : La clé privée est mal formatée

**Solution** :
- Vérifiez que vous avez copié TOUTE la clé, y compris les lignes BEGIN/END
- Vérifiez qu'il n'y a pas d'espaces ou de retours à la ligne en trop
- La clé doit commencer par `-----BEGIN` et finir par `-----END`

---

## 📚 Ressources

- [GitHub Actions - Using secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [SSH Key Authentication](https://www.ssh.com/academy/ssh/public-key-authentication)
- [Hostinger SSH Access](https://support.hostinger.com/en/articles/1583227-how-to-access-your-hosting-via-ssh)
