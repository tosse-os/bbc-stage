./go
npm run watch
npm run build



git add .
git commit -m "Final 27_04-1"
git push origin main


git pull origin main
git status


STRIPE_PUBLISHABLE_KEY=xxx
STRIPE_SECRET_KEY=xxx
STRIPE_PRICE_ID=xxx
STRIPE_WEBHOOK_SECRET=xxx
#stripe listen --forward-to https://dev.bbc.devel-web.de/wp-json/bloombridge/v1/stripe/webhook
