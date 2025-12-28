#!/bin/bash

# Define the plugin name and version
PLUGIN_SLUG="smart-content-fetcher"
BUILD_DIR="smart-content-fetcher-build"
ZIP_NAME="smart-content-fetcher.zip"

echo "[START] Starting build for $PLUGIN_SLUG..."

# 1. Build the React assets
echo "[BUILD] Building assets..."
npm run build

# 2. Prepare build directory
echo "[SETUP] Creating build directory..."
rm -rf $BUILD_DIR
mkdir $BUILD_DIR
mkdir $BUILD_DIR/$PLUGIN_SLUG

# 3. Copy files
echo "[COPY] Copying files..."
cp smart-content-fetcher.php $BUILD_DIR/$PLUGIN_SLUG/
cp readme.txt $BUILD_DIR/$PLUGIN_SLUG/
cp package.json $BUILD_DIR/$PLUGIN_SLUG/
cp -r build $BUILD_DIR/$PLUGIN_SLUG/
cp -r admin $BUILD_DIR/$PLUGIN_SLUG/
if [ -f "icon-256x256.png" ]; then
    cp icon-256x256.png $BUILD_DIR/$PLUGIN_SLUG/
fi

# Remove unwanted hidden/system files from the build dir
rm -f $BUILD_DIR/$PLUGIN_SLUG/.gitignore
rm -f $BUILD_DIR/$PLUGIN_SLUG/.editorconfig
rm -f $BUILD_DIR/$PLUGIN_SLUG/.DS_Store
rm -f $BUILD_DIR/$PLUGIN_SLUG/package.sh
rm -rf $BUILD_DIR/$PLUGIN_SLUG/.git

# 4. Create ZIP
echo "[ZIP] Zipping..."
cd $BUILD_DIR
# Using -x to strictly exclude unwanted files from the zip process itself, including this script
zip -r ../$ZIP_NAME $PLUGIN_SLUG -x "*.DS_Store" "*.git*" "*package.sh" "*__MACOSX*" "*.gitignore" "*.editorconfig" "*node_modules*"
cd ..

# 5. Cleanup
echo "[CLEANUP] Cleaning up..."
rm -rf $BUILD_DIR

echo "[DONE] Build complete! File created: $ZIP_NAME"
