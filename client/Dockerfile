FROM node:20-alpine AS javascript-builder
WORKDIR /app

# It's best to add as few files as possible before running the build commands
# as they will be re-run everytime one of those files changes.
#
# It's possible to run npm install with only the package.json and package-lock.json file.

ADD ./package.json ./package-lock.json ./
RUN npm install

ADD . /app/
RUN npm run build

FROM node:20-alpine
WORKDIR /app
COPY --from=javascript-builder /app/.output/ /app/
RUN ls /app/
CMD node ./server/index.mjs
