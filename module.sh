#!/bin/bash

# Création des dossiers principaux
mkdir -p app/Modules
mkdir -p app/Shared
mkdir -p app/Core

# Liste des modules
MODULES=("Authentication" "CourseManagement" "StudentPortal" "AssignmentSystem" "Notifications" "Analytics")

# Liste des sous-dossiers pour chaque module
SUBDIRS=(
    "Controllers"
    "Models"
    "Services"
    "Repositories"
    "Events"
    "Listeners"
    "Policies"
    "Database/Migrations"
    "Database/Seeders"
    "Tests/Unit"
    "Tests/Feature"
    "Routes"
    "Config"
    "Resources"
    "Jobs"
    "Providers"
)

# Création de la structure pour chaque module
for MODULE in "${MODULES[@]}"; do
    echo "Creating structure for module: $MODULE"
    
    # Création des sous-dossiers
    for SUBDIR in "${SUBDIRS[@]}"; do
        mkdir -p "app/Modules/$MODULE/$SUBDIR"
    done
    
    # Création des fichiers de base
    touch "app/Modules/$MODULE/Routes/api.php"
    touch "app/Modules/$MODULE/Routes/web.php"
    touch "app/Modules/$MODULE/Config/config.php"
    touch "app/Modules/$MODULE/Providers/${MODULE}ServiceProvider.php"
done

# Création de la structure partagée
SHARED_DIRS=(
    "Traits"
    "Interfaces"
    "Services"
    "Helpers"
    "Constants"
)

for DIR in "${SHARED_DIRS[@]}"; do
    mkdir -p "app/Shared/$DIR"
done

# Création de la structure Core
CORE_DIRS=(
    "Contracts"
    "Services"
    "Providers"
    "Middleware"
    "Console/Commands"
)

for DIR in "${CORE_DIRS[@]}"; do
    mkdir -p "app/Core/$DIR"
done

