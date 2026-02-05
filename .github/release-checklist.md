Release checklist
===========================================================

## Pre-release

- [ ] Composer: check if any dependencies/version constraints need updating - PR #xxx
- [ ] Add changelog for the release - PR #xxx
    :pencil2: Verify that a release link at the bottom of the `CHANGELOG.md` file has been added.

## Release

- [ ] Create PR to merge the `develop` branch into `main`.
- [ ] Merge that PR
- [ ] Make sure all CI builds are green.
- [ ] Create a release from the tag (careful, GH defaults to `develop`!) & copy & paste the changelog to it.
    Make sure to copy the links to the issues and the links to the GH usernames from the bottom of the changelog!
- [ ] Close the milestone.
- [ ] Open a new milestone for the next release.
- [ ] If any open PRs/issues which were milestoned for the release did not make it into the release, update their milestone.

## Announce

- [ ] Tweet about the release.


---

Additional actions to take, not part of the release checklist:
- [ ] Post a link to the release in the Yoast Slack.
- [ ] Update the test dependencies in Yoast packages.
