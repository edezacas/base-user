#!/usr/bin/env bash

git config --global credential.helper "cache --timeout=240"

echo

if ! [ -e .gemfury ]
 then
   echo "Cannot release: "
   echo
   echo ".gemfury file must be present containing Gemfury secret in order to make a new release."
   echo
   exit 1
fi

VERSION=$(sed -n -e '/"version":/ s/.*\: \"\(.*\)\",/\1/p' composer.json)
SECRET=$(sed -n -e '/secret/ s/.*\=//p' .gemfury)

if ! [[ $SECRET =~ ^.+$ ]]
  then
  echo "Cannot release: "
  echo
  echo ".gemfury file must contain line \"secret=<gemfury-secret>\""
  echo
  exit 1
fi

if ! [[ $VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]
  then
  echo "Cannot release: "
  echo
  echo "$VERSION is not a correct version number, cannot make a proper release"
  echo
  exit 1
fi

VERSION_EXISTS=$(git tag | sed -n -e "/v$VERSION/p")

if  [[ $VERSION_EXISTS ]]
  then
  echo "Cannot release: "
  echo
  echo "Version already exists (tag for version $VERSION already exists)"
  echo
  exit 1
fi

echo
echo "Releasing version $VERSION"
echo
echo "Building package"
echo

if [ -e package.zip ]
 then
   echo "Removing old package.zip"
   echo
   rm package.zip;
fi

zip -r package.zip ./src CHANGELOG.md README.md composer.json

echo
echo "Uploading package to Gemfury"
echo

curl -F package=@package.zip https://$SECRET@push.fury.io/digitalascetic/

echo
echo "Creating tag v${VERSION}"
echo

CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)

git tag v$VERSION
echo
git push
echo
git push --tags
echo

echo
