# Architecture

The Tenant Maintenance Escalation Console has two layers:

1. **WordPress / portal primitive**
   - shortcode for a maintenance snapshot
   - REST endpoint for machine-readable service and escalation state

2. **Static operator surface**
   - prerendered HTML routes for overview, maintenance lane, escalation queue, service posture, verification, and docs
   - generated API JSON snapshots for summary, ticket lanes, vendor escalations, and verification gates

## Purpose

The plugin demonstrates how reviewed maintenance state can be exposed from WordPress or an adjacent property portal. The static bundle demonstrates how that same state can be shaped into a buyer-readable operator surface for public portfolio proof.
