#!/bin/bash

# Prompt the user for a version number
echo "Releasing agnostic-blocks"

# Define a subfolder name and zip file name
SUBFOLDER="agnostic-blocks-child-base"
ZIP_NAME="agnostic-blocks-child-base.zip"

# Create the subfolder if it doesn't exist
mkdir -p "$SUBFOLDER"

# Copy the files into the subfolder, excluding unwanted files and directories
rsync -av --exclude={.git,.gitignore,.DS_Store,agnostic-blocks} ./ "$SUBFOLDER/"


# Check if rsync was successful
if [[ $? -ne 0 ]]; then
  echo "File copy failed. Exiting."
  exit 1
fi

# Zip the subfolder
zip -r "$ZIP_NAME" "$SUBFOLDER/"

# Check if zip was successful
if [[ $? -ne 0 ]]; then
  echo "Zipping failed. Exiting."
  exit 1
fi

# Optionally, remove the subfolder after zipping
rm -rf "$SUBFOLDER"

# Confirm successful completion
echo "Successfully created release: $ZIP_NAME"