#!/bin/bash
set -e

ENVIRON=${1:-develop}
K8S_NAMESPACE=${2:-v3}
helm uninstall --namespace ${K8S_NAMESPACE} v3-api-${ENVIRON}
