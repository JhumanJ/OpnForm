# Check if act is installed, if not, install it
if ! command -v act &> /dev/null
then
    echo "act could not be found, installing..."
    curl -s https://raw.githubusercontent.com/nektos/act/master/install.sh | sudo bash
fi

LATEST_TAG=$(git describe --tags $(git rev-list --tags --max-count=1))

echo "Building image with tag: ${1:-$LATEST_TAG}"
act \
    -W '.github/workflows/docker-build.yaml' \
    -s GITHUB_TOKEN="$(gh auth token)" \
    --var IMAGE_TAG_NAME=${1:-$LATEST_TAG} | grep --color=always -v '::'
