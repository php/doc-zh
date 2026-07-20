#!/usr/bin/env bash
#
# Track changes in php/doc-en and create issues in doc-zh
# for files that need translation updates.
#
# How it works:
#   - Fetches all commits on doc-en master from the last 7 days
#   - For each commit, checks if a matching issue already exists in doc-zh
#   - If not, creates one listing the ZH files to update
#
# Why iterate commits instead of merged PRs: maintainers sometimes push
# directly to master without going through a PR (e.g. cherry-picking a
# contributor's patch). Those changes are real translation work but the
# PR-based tracker misses them. Walking the commit log catches both cases.
#
# Why 7 days: the action runs daily, so 7 days gives us 6 days of margin.
# Even if the action fails for a whole week, nothing is missed.
# Duplicates are prevented by searching for the commit SHA (or associated PR
# number for backward compat) in existing issue titles/bodies.
#
set -euo pipefail

# Ensure the sync-en label exists (idempotent)
gh label create "sync-en" --color "#0075ca" --description "Track EN changes" \
  --repo "$GH_REPO" 2>/dev/null || true

SINCE=$(date -u -d '7 days ago' '+%Y-%m-%dT%H:%M:%SZ')
echo "Checking doc-en commits on master since $SINCE"

# Fetch all commits on master from the last 7 days (paginate if needed)
PAGE=1
ALL_SHAS=""
while :; do
  BATCH=$(gh api "repos/php/doc-en/commits?sha=master&since=$SINCE&per_page=100&page=$PAGE" \
    --jq '.[].sha' 2>/dev/null || true)
  COUNT=$(echo "$BATCH" | grep -c . 2>/dev/null || echo "0")
  if [ -n "$BATCH" ]; then
    ALL_SHAS="$ALL_SHAS"$'\n'"$BATCH"
  fi
  echo "  Page $PAGE: $COUNT commit(s)"
  if [ "$COUNT" -lt 100 ]; then
    break
  fi
  PAGE=$((PAGE + 1))
done

echo "$ALL_SHAS" | sed '/^$/d' > /tmp/commits.txt
TOTAL=$(wc -l < /tmp/commits.txt)
echo "Total: $TOTAL commit(s) on master in the last 7 days"

if [ "$TOTAL" -eq 0 ]; then
  echo "Nothing to do."
  exit 0
fi

CREATED=0
SKIPPED=0

while read -r SHA; do
  [ -z "$SHA" ] && continue

  SHORT_SHA=${SHA:0:7}

  # Get commit info (single API call)
  COMMIT_DATA=$(gh api "repos/php/doc-en/commits/$SHA" \
    --jq '{msg: (.commit.message | split("\n")[0]), date: .commit.author.date, author: (.author.login // .commit.author.name), files: [.files[].filename]}' 2>/dev/null)
  COMMIT_MSG=$(echo "$COMMIT_DATA" | jq -r '.msg')
  COMMIT_DATE=$(echo "$COMMIT_DATA" | jq -r '.date' | cut -dT -f1)
  COMMIT_AUTHOR=$(echo "$COMMIT_DATA" | jq -r '.author')

  echo ""
  echo "Commit $SHORT_SHA by $COMMIT_AUTHOR: $COMMIT_MSG"

  # Skip commits marked with [skip-revcheck]
  if echo "$COMMIT_MSG" | grep -qi '\[skip-revcheck\]'; then
    echo "  -> [skip-revcheck], skipping."
    SKIPPED=$((SKIPPED + 1))
    continue
  fi

  # Deduplication: search for the full commit SHA in existing issues
  EXISTING=$(gh issue list --repo "$GH_REPO" --search "\"$SHA\"" \
    --state all --json number --jq 'length')
  if [ "$EXISTING" -gt 0 ]; then
    echo "  -> Issue already exists (by SHA), skipping."
    SKIPPED=$((SKIPPED + 1))
    continue
  fi

  # Backward compat: if commit is associated with a PR, also check by PR number
  PR_NUMBER=$(gh api "repos/php/doc-en/commits/$SHA/pulls" \
    --jq '.[0].number // empty' 2>/dev/null || true)
  if [ -n "$PR_NUMBER" ]; then
    EXISTING=$(gh issue list --repo "$GH_REPO" --search "\"doc-en/pull/$PR_NUMBER\"" \
      --state all --json number --jq 'length')
    if [ "$EXISTING" -gt 0 ]; then
      echo "  -> Issue already exists (by PR #$PR_NUMBER), skipping."
      SKIPPED=$((SKIPPED + 1))
      continue
    fi
  fi

  # Get files changed in this commit
  FILES=$(echo "$COMMIT_DATA" | jq -r '.files[]')

  if [ -z "$FILES" ]; then
    echo "  -> No files found, skipping."
    continue
  fi

  # Categorize files
  UPDATE_LIST=""
  NEW_LIST=""

  while IFS= read -r FILE; do
    if [[ "$FILE" == */versions.xml ]]; then
      continue
    fi
    if [ -f "$FILE" ]; then
      UPDATE_LIST="${UPDATE_LIST}- \`${FILE}\`"$'\n'
    elif [[ "$FILE" == *.xml ]]; then
      NEW_LIST="${NEW_LIST}- \`${FILE}\`"$'\n'
    fi
  done <<< "$FILES"

  # Skip if no ES-relevant files
  if [ -z "$UPDATE_LIST" ] && [ -z "$NEW_LIST" ]; then
    echo "  -> No ZH-relevant files, skipping."
    continue
  fi

  # Build issue body (URLs in backticks to avoid crosslinks)
  BODY="Commit: \`https://github.com/php/doc-en/commit/$SHA\`"$'\n'
  if [ -n "$PR_NUMBER" ]; then
    BODY+="PR: \`https://github.com/php/doc-en/pull/$PR_NUMBER\`"$'\n'
  fi

  if [ -n "$UPDATE_LIST" ]; then
    BODY+=$'\n'"**Archivos ZH files to update**"$'\n'
    BODY+="$UPDATE_LIST"
  fi

  if [ -n "$NEW_LIST" ]; then
    BODY+=$'\n'"**New EN files (not yet translated)**"$'\n'
    BODY+="$NEW_LIST"
  fi

  # Create the issue
  ISSUE_TITLE="[Sync EN] $COMMIT_MSG"
  echo "$BODY" | gh issue create \
    --repo "$GH_REPO" \
    --title "$ISSUE_TITLE" \
    --label "sync-en" \
    --body-file -

  echo "  -> Issue created."
  CREATED=$((CREATED + 1))
done < /tmp/commits.txt

echo ""
echo "Done. Created: $CREATED, Skipped: $SKIPPED"
