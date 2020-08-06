#!/bin/bash
set -e

function main() {
    ARGS=`getArgs "$@"`

    TAG=`echo "$ARGS" | getNamedArg tag`
    K8S_NAMESPACE=`echo "$ARGS" | getNamedArg k8s-namespace`
    ENVIRON=`echo "$ARGS" | getNamedArg env`
    HOST=`echo "$ARGS" | getNamedArg host`
    SSL_PRIVATE_KEY=`echo "$ARGS" | getNamedArg ssl-key`
    SSL_CERTIFICATE=`echo "$ARGS" | getNamedArg ssl-crt`
    MYSQL_USER=`echo "$ARGS" | getNamedArg mysql-user`
    MYSQL_PASSWORD=`echo "$ARGS" | getNamedArg mysql-password`
    MYSQL_DATABASE=`echo "$ARGS" | getNamedArg mysql-database`
    MYSQL_HOST=`echo "$ARGS" | getNamedArg mysql-host`
    REGISTRY_SECRET=`echo "$ARGS" | getNamedArg reg-secret`
    SSL_CLUSTER_ISSUER=`echo "$ARGS" | getNamedArg ssl-cluster-issuer`
    LIMITS_CPU=`echo "$ARGS" | getNamedArg resources.limits.cpu`
    LIMITS_MEMORY=`echo "$ARGS" | getNamedArg resources.limits.memory`
    REQUESTS_CPU=`echo "$ARGS" | getNamedArg resources.requests.cpu`
    REQUESTS_MEMORY=`echo "$ARGS" | getNamedArg resources.requests.memory`

    # Validation
    if [ -z "$MYSQL_HOST" ]; then
        echo "Argument --mysql-host is obligatory"
        exit 1
    fi

    if [ -z "$REGISTRY_SECRET" ]; then
        echo "Argument --reg-secret is obligatory"
        exit 1
    fi

    # Folder Structure
    DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
    CHART_DIR="${DIR}/../chart"
    SSL_DIR="${DIR}/../ssl"

    # Database
    MYSQL_USER=${MYSQL_USER:-v3}
    MYSQL_PASSWORD=${MYSQL_PASSWORD:-Stron6PasswD}
    MYSQL_DATABASE=${MYSQL_DATABASE:-v3}

    # Defaults
    TAG=${TAG:-latest}
    K8S_NAMESPACE=${K8S_NAMESPACE:-v3}
    ENVIRON=${ENVIRON:-develop}
    LOG_DESTINATION=${LOG_DESTINATION:-php://stdout}
    LOG_FORMAT=${LOG_FORMAT:-json}
    HOST=${HOST:-${ENVIRON}.api.v3.spherewms.com}

    HELM_ARGS="--namespace ${K8S_NAMESPACE} \
      --debug \
      --set registryConfigJsonBase64=${REGISTRY_SECRET} \
      --set image.tag=${TAG} \
      --set nginx.image.tag=${TAG} \
      --set ingress.enabled=true \
      --set ingress.hosts[0].host=${HOST} \
      --set ingress.hosts[0].paths[0]=/ \
      --set ingress.tls[0].hosts[0]=${HOST} \
      --set extraEnv.DB_DATABASE=${MYSQL_DATABASE} \
      --set extraEnv.DB_USERNAME=${MYSQL_USER} \
      --set extraEnv.DB_PASSWORD=${MYSQL_PASSWORD} \
      --set dbHost=${MYSQL_HOST} \
      --set publicStorage.nfs.server=\"192.168.254.191\" \
      --set publicStorage.nfs.path=\"/nfs1/v3/api/tmp/non_prod\"
      "

    SSL_PRIVATE_KEY=${SSL_PRIVATE_KEY:-${SSL_DIR}/spherewms.com.key}
    SSL_CERTIFICATE=${SSL_CERTIFICATE:-${SSL_DIR}/spherewms.com.crt}

    if [ -f "$SSL_PRIVATE_KEY" ] && [ -f "$SSL_CERTIFICATE" ]; then
        HELM_ARGS="${HELM_ARGS} --set-file tls.key=${SSL_PRIVATE_KEY} --set-file tls.crt=${SSL_CERTIFICATE} --set tls.enabled=true"
    else
        if [ ! -z "$SSL_CLUSTER_ISSUER" ]; then
            HELM_ARGS="${HELM_ARGS} --set tls.clusterIssuer=${SSL_CLUSTER_ISSUER} --set tls.enabled=true"
        fi
    fi

    if [ ! -z "$LIMITS_CPU" ]; then
      HELM_ARGS="${HELM_ARGS} --set resources.limits.cpu=${LIMITS_CPU}"
    fi
    if [ ! -z "$LIMITS_MEMORY" ]; then
      HELM_ARGS="${HELM_ARGS} --set resources.limits.memory=${LIMITS_MEMORY}"
    fi
    if [ ! -z "$REQUESTS_CPU" ]; then
      HELM_ARGS="${HELM_ARGS} --set resources.requests.cpu=${REQUESTS_CPU}"
    fi
    if [ ! -z "$REQUESTS_MEMORY" ]; then
      HELM_ARGS="${HELM_ARGS} --set resources.requests.memory=${REQUESTS_MEMORY}"
    fi

    for varname in ${!API_EXTRA_ENV*}
    do
      KEY=${varname#API_EXTRA_ENV_}
      VAL=${!varname}
      HELM_ARGS="${HELM_ARGS} --set extraEnv.${KEY}=${VAL}"
    done

    INSTALL_ARGS="v3-api-${ENVIRON} $CHART_DIR ${HELM_ARGS}"

    echo $INSTALL_ARGS
    helm upgrade --install --wait ${INSTALL_ARGS}
    # helm template ${INSTALL_ARGS}
    exit 0
}

function getArgs() {
    for arg in "$@"; do
        echo "$arg"
    done
}

function getNamedArg() {
    ARG_NAME=$1

    sed --regexp-extended --quiet --expression="
        s/^--$ARG_NAME=(.*)\$/\1/p  # Get arguments in format '--arg=value': [s]ubstitute '--arg=value' by 'value', and [p]rint
        /^--$ARG_NAME\$/ {          # Get arguments in format '--arg value' ou '--arg'
            n                       # - [n]ext, because in this format, if value exists, it will be the next argument
            /^--/! p                # - If next doesn't starts with '--', it is the value of the actual argument
            /^--/ {                 # - If next do starts with '--', it is the next argument and the actual argument is a boolean one
                # Then just repla[c]ed by TRUE
                c TRUE
            }
        }
    "
}

main "$@"
