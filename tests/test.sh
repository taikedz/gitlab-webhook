die() {
    echo "$*"
    exit 1
}

url="${1:-}" ; shift || die "Specify the URL"
token="${1:-}" ; shift || die "Specify the token"

wget --post-file=request-body.json \
 --header="Content-Type: application/json" \
 --header="X-Gitlab-Event: Merge Request Hook" \
 --header="X-Gitlab-Token: $token" \
 -S "$url"
