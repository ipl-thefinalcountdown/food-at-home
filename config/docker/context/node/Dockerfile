FROM node:15.3.0-alpine3.12

# Add required depeendencies for running
RUN apk --update --no-cache add nodejs npm

# Delete cache
RUN rm -rf /tmp/* /var/cache/apk/*

ENTRYPOINT npm install && npm run watch
