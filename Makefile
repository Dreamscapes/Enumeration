# Dreamscapes\Enumeration
#
# Licensed under the BSD-3-Clause license
# For full copyright and license information, please see the LICENSE file
#
# @author       Robert Rossmann <rr.rossmann@me.com>
# @copyright    2014 Robert Rossmann
# @link         https://github.com/Dreamscapes/Enumeration
# @license      http://choosealicense.com/licenses/BSD-3-Clause  BSD-3-Clause License

# Helper vars
BIN = vendor/bin/

# Project-specific information
GH_USER = Alaneor
GH_REPO = Dreamscapes/Enumeration

# Project-specific paths
TMPDIR = tmp
DOCDIR = docs
COVDIR = coverage
GHPDIR = gh-pages

# Set/override some variables for Travis

# Travis cannot access our repo using just a username - a token is necessary to be exported into
# GH_TOKEN env variable
GH_USER := $(if ${GH_TOKEN},${GH_TOKEN},$(GH_USER))
# This will usually not change, but if someone forks our repo, this should make sure Travis will
# not try to update the source repo
GH_REPO := $(if ${TRAVIS_REPO_SLUG},${TRAVIS_REPO_SLUG},$(GH_REPO))

# Default - Run it all!
all: lint test docs

# Install dependencies (added for compatibility reasons with usual workflows with make,
# i.e. calling make && make install)
install:
	@composer install

# Run tests using PHPUnit
test: clean-coverage
	@$(BIN)phpunit

# Lint all php files using PHP_CodeSniffer
lint:
	@$(BIN)phpcs --standard=PSR2 --ignore=vendor/*,docs/* -p Dreamscapes

# Generate API documentation
docs: clean-docs
	@$(BIN)phpdoc

# Send code coverage information to Coveralls.io
coveralls: test
	@$(BIN)coveralls -v

# Update gh-pages branch with new docs
gh-pages: clean-gh-pages docs
	@# The commit message when updating gh-pages
	$(eval COMMIT_MSG := $(if ${TRAVIS_COMMIT},\
		"Updated gh-pages from ${TRAVIS_COMMIT}",\
		"Updated gh-pages manually"))

	@git clone --branch=$(GHPDIR) \
			https://$(GH_USER)@github.com/$(GH_REPO).git $(GHPDIR) > /dev/null 2>&1; \
		cd $(GHPDIR); \
		rm -rf *; \
		cp -Rf ../$(DOCDIR)/* .; \
		git add -A; \
		[[ -n "${TRAVIS}" ]] && \
			git config user.name "Travis-CI" && \
			git config user.email "travis@travis-ci.org"; \
		git commit -m $(COMMIT_MSG); \
		git push --quiet origin $(GHPDIR) > /dev/null 2>&1;

# Delete API docs
clean-docs:
	@rm -rf $(TMPDIR)
	@rm -rf $(DOCDIR)

# Delete coverage results
clean-coverage:
	@rm -rf $(COVDIR)

# Clean gh-pages dir
clean-gh-pages:
	@rm -rf $(GHPDIR)

# Delete all generated files
clean: clean-docs clean-coverage clean-gh-pages

.PHONY: test lint gh-pages clean-docs clean-coverage clean-gh-pages
