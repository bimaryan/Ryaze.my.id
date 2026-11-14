FROM node:20-bookworm-slim

# PM2 sebagai process manager aplikasi user (agent punya fallback spawn bila PM2 tidak ada)
RUN npm install -g pm2@latest --no-audit --no-fund

WORKDIR /agent
COPY agent.js package.json ./

ENV NODE_ENV=production \
    AGENT_PORT=4700 \
    CLIENTS_DIR=/www/sites/hosting_clients

EXPOSE 4700

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD node -e "require('http').get('http://127.0.0.1:4700/health', r => process.exit(r.statusCode === 200 ? 0 : 1)).on('error', () => process.exit(1))"

CMD ["node", "agent.js"]
